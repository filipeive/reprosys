<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function __construct(private FinancialService $financialService)
    {
    }

    /**
     * Exibir lista de despesas com filtros
     */
    public function index(Request $request)
    {
        $query = Expense::with(['user', 'category', 'financialAccount']);

        // Se não for admin nem gerente, vê apenas as suas próprias despesas
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('view_all_expenses')) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest()->paginate(10);

        $totalExpenses = (clone $query)->sum('amount');
        $averageExpense = (clone $query)->avg('amount');
        $highestExpense = (clone $query)->max('amount') ?: 0;
        $lowestExpense = (clone $query)->min('amount') ?: 0;

        // Carregar categorias para o offcanvas
        $categories = ExpenseCategory::all();
        $financialAccounts = FinancialAccount::where('is_active', true)->orderBy('sort_order')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $expenses->items(),
                'pagination' => [
                    'current_page' => $expenses->currentPage(),
                    'last_page' => $expenses->lastPage(),
                    'total' => $expenses->total(),
                    'per_page' => $expenses->perPage(),
                    'from' => $expenses->firstItem(),
                    'to' => $expenses->lastItem(),
                ]
            ]);
        }

        return view('expenses.index', compact(
            'expenses', 'totalExpenses', 'averageExpense', 'highestExpense', 'lowestExpense', 'categories', 'financialAccounts'
        ));
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        // Redirecionar para o index que agora usa modais
        return redirect()->route('expenses.index');
    }

    /**
     * Armazenar nova despesa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $expense = Expense::create([
            'user_id' => auth()->id(),
            'expense_category_id' => $validated['expense_category_id'],
            'financial_account_id' => $validated['financial_account_id'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_number' => $validated['receipt_number'],
            'notes' => $validated['notes'],
        ]);

        $this->financialService->syncExpenseTransaction($expense);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa registrada com sucesso!',
                'data' => $expense->load(['user', 'category', 'financialAccount'])
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa registrada com sucesso!');
    }

    /**
     * Exibir despesa específica
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'financialAccount']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(Expense $expense)
    {
        //$this->authorize('update-expense', $expense);

        $categories = ExpenseCategory::all();
        $financialAccounts = FinancialAccount::where('is_active', true)->orderBy('sort_order')->get();
        return view('expenses.edit', compact('expense', 'categories', 'financialAccounts'));
    }

    /**
     * Atualizar despesa
     */
    public function update(Request $request, Expense $expense)
    {
        // Verificar permissão
        //$this->authorize('update-expense', $expense);

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $expense->update($validated);
        $this->financialService->syncExpenseTransaction($expense->fresh());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa atualizada com sucesso!',
                'data' => $expense->fresh()->load(['user', 'category', 'financialAccount'])
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remover despesa
     */
    public function destroy(Expense $expense , Request $request)
    {
        //$this->authorize('delete', $expense);

        // Opcional: verificar se pode ser excluída
        $this->financialService->removeTransactionsForReference(Expense::class, $expense->id);
        $expense->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa excluída com sucesso!'
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
