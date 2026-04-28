<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\SalaryPayment;
use App\Models\Sale;
use Carbon\Carbon;

class FinancialService
{
    public function transactionTypes(): array
    {
        return [
            'owner_investment' => ['label' => 'Aporte do Proprietário', 'direction' => 'in'],
            'debt_payment_receipt' => ['label' => 'Recebimento Manual', 'direction' => 'in'],
            'other_income' => ['label' => 'Outra Entrada', 'direction' => 'in'],
            'sale_receipt' => ['label' => 'Recebimento de Venda', 'direction' => 'in'],
            'salary_payment' => ['label' => 'Pagamento de Salário', 'direction' => 'out'],
            'expense_payment' => ['label' => 'Pagamento de Despesa', 'direction' => 'out'],
            'money_debt_disbursement' => ['label' => 'Empréstimo em Dinheiro', 'direction' => 'out'],
            'owner_withdrawal' => ['label' => 'Retirada do Proprietário', 'direction' => 'out'],
            'cash_adjustment_out' => ['label' => 'Ajuste de Caixa (-)', 'direction' => 'out'],
            'cash_adjustment_in' => ['label' => 'Ajuste de Caixa (+)', 'direction' => 'in'],
            'other_outflow' => ['label' => 'Outra Saída', 'direction' => 'out'],
        ];
    }

    public function transactionDirection(string $type): string
    {
        return $this->transactionTypes()[$type]['direction'] ?? 'out';
    }

    public function manualTransactionTypes(): array
    {
        return array_intersect_key($this->transactionTypes(), array_flip([
            'owner_investment',
            'debt_payment_receipt',
            'other_income',
            'owner_withdrawal',
            'cash_adjustment_out',
            'cash_adjustment_in',
            'other_outflow',
        ]));
    }

    public function transactionLabel(string $type): string
    {
        return $this->transactionTypes()[$type]['label'] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public function getDefaultAccountForPaymentMethod(?string $paymentMethod): ?FinancialAccount
    {
        $slug = match ($paymentMethod) {
            'cash', 'card', 'transfer', null => 'caixa-principal',
            'mpesa', 'emola' => 'carteira-movel',
            default => null,
        };

        if (!$slug) {
            return null;
        }

        return FinancialAccount::where('slug', $slug)->where('is_active', true)->first();
    }

    public function createManualTransaction(array $data): FinancialTransaction
    {
        return FinancialTransaction::create([
            'financial_account_id' => $data['financial_account_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'type' => $data['type'],
            'direction' => $data['direction'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'],
            'description' => $data['description'],
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function createSalaryPayment(array $data): SalaryPayment
    {
        $transaction = $this->createManualTransaction([
            'financial_account_id' => $data['financial_account_id'],
            'user_id' => $data['paid_by'] ?? auth()->id(),
            'type' => 'salary_payment',
            'direction' => 'out',
            'amount' => $data['amount'],
            'transaction_date' => $data['payment_date'],
            'description' => $data['description'],
            'reference_type' => SalaryPayment::class,
            'reference_id' => null,
            'payment_method' => 'cash',
            'notes' => $data['notes'] ?? null,
        ]);

        $salaryPayment = SalaryPayment::create([
            'user_id' => $data['user_id'],
            'financial_account_id' => $data['financial_account_id'],
            'financial_transaction_id' => $transaction->id,
            'paid_by' => $data['paid_by'] ?? auth()->id(),
            'amount' => $data['amount'],
            'base_amount' => $data['base_amount'] ?? $data['amount'],
            'variable_amount' => $data['variable_amount'] ?? 0,
            'payment_date' => $data['payment_date'],
            'reference_month' => $data['reference_month'] ?? null,
            'description' => $data['description'],
            'notes' => $data['notes'] ?? null,
        ]);

        $transaction->update([
            'reference_id' => $salaryPayment->id,
        ]);

        return $salaryPayment;
    }

    public function syncSaleTransaction(Sale $sale): void
    {
        $this->removeTransactionsForReference(Sale::class, $sale->id);

        if ($sale->payment_method === 'credit') {
            return;
        }

        $account = $this->getDefaultAccountForPaymentMethod($sale->payment_method);

        if (!$account) {
            return;
        }

        $this->createManualTransaction([
            'financial_account_id' => $account->id,
            'user_id' => $sale->user_id,
            'type' => 'sale_receipt',
            'direction' => 'in',
            'amount' => $sale->total_amount,
            'transaction_date' => optional($sale->sale_date)->format('Y-m-d') ?? now()->toDateString(),
            'description' => "Recebimento da venda #{$sale->id}",
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'payment_method' => $sale->payment_method,
            'notes' => $sale->notes,
        ]);
    }

    public function syncExpenseTransaction(Expense $expense): void
    {
        $this->removeTransactionsForReference(Expense::class, $expense->id);

        $account = $expense->financialAccount ?: FinancialAccount::find($expense->financial_account_id);

        if (!$account) {
            $account = $this->getDefaultAccountForPaymentMethod('cash');
        }

        if (!$account) {
            return;
        }

        $this->createManualTransaction([
            'financial_account_id' => $account->id,
            'user_id' => $expense->user_id,
            'type' => 'expense_payment',
            'direction' => 'out',
            'amount' => $expense->amount,
            'transaction_date' => optional($expense->expense_date)->format('Y-m-d') ?? now()->toDateString(),
            'description' => $expense->description,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'payment_method' => 'cash',
            'notes' => $expense->notes,
        ]);
    }

    public function syncDebtPaymentTransaction(DebtPayment $payment): void
    {
        $this->removeTransactionsForReference(DebtPayment::class, $payment->id);

        $account = $this->getDefaultAccountForPaymentMethod($payment->payment_method);

        if (!$account) {
            return;
        }

        $this->createManualTransaction([
            'financial_account_id' => $account->id,
            'user_id' => $payment->user_id,
            'type' => 'debt_payment_receipt',
            'direction' => 'in',
            'amount' => $payment->amount,
            'transaction_date' => optional($payment->payment_date)->format('Y-m-d') ?? now()->toDateString(),
            'description' => "Recebimento da dívida #{$payment->debt_id}",
            'reference_type' => DebtPayment::class,
            'reference_id' => $payment->id,
            'payment_method' => $payment->payment_method,
            'notes' => $payment->notes,
        ]);
    }

    public function syncMoneyDebtDisbursement(Debt $debt): void
    {
        $this->removeTypedReferenceTransactions(Debt::class, $debt->id, 'money_debt_disbursement');

        if (!$debt->isMoneyDebt()) {
            return;
        }

        $account = $this->getDefaultAccountForPaymentMethod('cash');

        if (!$account) {
            return;
        }

        $this->createManualTransaction([
            'financial_account_id' => $account->id,
            'user_id' => $debt->user_id,
            'type' => 'money_debt_disbursement',
            'direction' => 'out',
            'amount' => $debt->original_amount,
            'transaction_date' => optional($debt->debt_date)->format('Y-m-d') ?? now()->toDateString(),
            'description' => "Dinheiro entregue em dívida #{$debt->id}",
            'reference_type' => Debt::class,
            'reference_id' => $debt->id,
            'payment_method' => 'cash',
            'notes' => $debt->notes,
        ]);
    }

    public function removeTransactionsForReference(string $referenceType, int $referenceId): void
    {
        FinancialTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }

    public function removeTypedReferenceTransactions(string $referenceType, int $referenceId, string $type): void
    {
        FinancialTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('type', $type)
            ->delete();
    }

    public function getMonthSummary(?Carbon $date = null): array
    {
        $date ??= now();

        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        // Exclude adjustment transactions from month summary to avoid inflating real transaction totals
        $inflows = (float) FinancialTransaction::whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->where('direction', 'in')
            ->whereNotIn('type', ['cash_adjustment_in'])
            ->sum('amount');

        $outflows = (float) FinancialTransaction::whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->where('direction', 'out')
            ->whereNotIn('type', ['cash_adjustment_out'])
            ->sum('amount');

        return [
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net' => $inflows - $outflows,
        ];
    }
}
