<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(private FinancialService $financialService)
    {
    }

    public function index()
    {
        $accounts = FinancialAccount::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $accounts = $accounts->map(function (FinancialAccount $account) {
            $account->setAttribute('current_balance', $account->current_balance);
            return $account;
        });

        $currentCapital = (float) $accounts->sum('current_balance');
        $receivables = (float) Debt::where('status', 'active')->sum('remaining_amount');
        $monthSummary = $this->financialService->getMonthSummary();

        $todayInflow = (float) FinancialTransaction::whereDate('transaction_date', today())
            ->where('direction', 'in')
            ->sum('amount');
        $todayOutflow = (float) FinancialTransaction::whereDate('transaction_date', today())
            ->where('direction', 'out')
            ->sum('amount');

        $transactionTypes = [
            'owner_investment' => ['label' => 'Aporte do Proprietário', 'direction' => 'in'],
            'debt_payment_receipt' => ['label' => 'Recebimento Manual', 'direction' => 'in'],
            'other_income' => ['label' => 'Outra Entrada', 'direction' => 'in'],
            'salary_payment' => ['label' => 'Pagamento de Salário', 'direction' => 'out'],
            'owner_withdrawal' => ['label' => 'Retirada do Proprietário', 'direction' => 'out'],
            'cash_adjustment_out' => ['label' => 'Ajuste de Caixa (-)', 'direction' => 'out'],
            'cash_adjustment_in' => ['label' => 'Ajuste de Caixa (+)', 'direction' => 'in'],
            'other_outflow' => ['label' => 'Outra Saída', 'direction' => 'out'],
        ];

        $recentTransactions = FinancialTransaction::with(['account', 'user'])
            ->orderByDesc('transaction_date')
            ->latest('id')
            ->limit(15)
            ->get();

        $dailyFlow = FinancialTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN direction = 'in' THEN amount ELSE 0 END) as inflows"),
                DB::raw("SUM(CASE WHEN direction = 'out' THEN amount ELSE 0 END) as outflows")
            )
            ->whereDate('transaction_date', '>=', Carbon::today()->subDays(6))
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->get()
            ->keyBy('date');

        $cashFlowLabels = [];
        $cashFlowInflows = [];
        $cashFlowOutflows = [];

        for ($date = Carbon::today()->subDays(6); $date->lte(Carbon::today()); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $cashFlowLabels[] = $date->format('d/m');
            $cashFlowInflows[] = (float) optional($dailyFlow->get($dateKey))->inflows;
            $cashFlowOutflows[] = (float) optional($dailyFlow->get($dateKey))->outflows;
        }

        return view('finances.index', compact(
            'accounts',
            'currentCapital',
            'receivables',
            'monthSummary',
            'todayInflow',
            'todayOutflow',
            'recentTransactions',
            'transactionTypes',
            'cashFlowLabels',
            'cashFlowInflows',
            'cashFlowOutflows',
        ));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $direction = match ($validated['type']) {
            'owner_investment', 'debt_payment_receipt', 'other_income', 'cash_adjustment_in' => 'in',
            default => 'out',
        };

        $this->financialService->createManualTransaction([
            'financial_account_id' => $validated['financial_account_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'direction' => $direction,
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('finances.index')->with('success', 'Movimento financeiro registrado com sucesso.');
    }
}
