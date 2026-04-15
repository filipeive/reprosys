<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\UserActivity;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $transactionTypes = $this->financialService->transactionTypes();
        $manualTransactionTypes = $this->financialService->manualTransactionTypes();

        $todayInflow = (float) FinancialTransaction::whereDate('transaction_date', today())
            ->where('direction', 'in')
            ->sum('amount');
        $todayOutflow = (float) FinancialTransaction::whereDate('transaction_date', today())
            ->where('direction', 'out')
            ->sum('amount');

        $filters = [
            'date_from' => request('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to' => request('date_to', now()->format('Y-m-d')),
            'financial_account_id' => request('financial_account_id'),
            'direction' => request('direction'),
            'type' => request('type'),
            'search' => request('search'),
        ];

        $transactions = FinancialTransaction::with(['account', 'user'])
            ->whereBetween('transaction_date', [$filters['date_from'], $filters['date_to']])
            ->when($filters['financial_account_id'], fn ($query, $accountId) => $query->where('financial_account_id', $accountId))
            ->when($filters['direction'], fn ($query, $direction) => $query->where('direction', $direction))
            ->when($filters['type'], fn ($query, $type) => $query->where('type', $type))
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('description', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

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
            'transactions',
            'transactionTypes',
            'manualTransactionTypes',
            'filters',
            'cashFlowLabels',
            'cashFlowInflows',
            'cashFlowOutflows',
        ));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'type' => ['required', 'string', 'max:50', Rule::in(array_keys($this->financialService->manualTransactionTypes()))],
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $transaction = $this->financialService->createManualTransaction([
            'financial_account_id' => $validated['financial_account_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'direction' => $this->financialService->transactionDirection($validated['type']),
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'financial_transaction_create',
            'model_type' => FinancialTransaction::class,
            'model_id' => $transaction->id,
            'description' => "Registrou movimento financeiro manual: {$transaction->description}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('finances.index')->with('success', 'Movimento financeiro registrado com sucesso.');
    }

    public function updateAccount(Request $request, FinancialAccount $account)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'opening_balance' => 'required|numeric',
        ]);

        $account->update([
            'opening_balance' => $validated['opening_balance'],
        ]);

        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'financial_transaction_create',
            'model_type' => FinancialAccount::class,
            'model_id' => $account->id,
            'description' => "Atualizou saldo inicial da conta {$account->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('finances.index')->with('success', "Saldo inicial da conta {$account->name} atualizado com sucesso.");
    }

    public function show(FinancialTransaction $transaction)
    {
        $user = auth()->user();
        $canViewDetails = $user && ($user->isAdmin() || $user->isManager() || (int) $transaction->user_id === (int) $user->id);

        abort_unless($canViewDetails, 403);

        $transaction->load(['account', 'user']);

        return view('finances.show', compact('transaction'));
    }
}
