<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private FinancialService $financialService) {}

    /**
     * Exibir lista de despesas com filtros
     */
    public function index(Request $request)
    {
        $query = Expense::with(['user', 'category', 'financialAccount']);

        // Se não for admin nem gerente, vê apenas as suas próprias despesas
        if (! auth()->user()->isAdmin() && ! auth()->user()->hasPermission('view_all_expenses')) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
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
        $products = Product::where('type', 'product')->where('is_active', true)->orderBy('name')->get();

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
                ],
            ]);
        }

        return view('expenses.index', compact(
            'expenses', 'totalExpenses', 'averageExpense', 'highestExpense', 'lowestExpense', 'categories', 'financialAccounts', 'products'
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
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = $request->filled('product_id') ? $request->product_id : null;
        $quantity = $request->filled('quantity') ? $request->quantity : 1;

        $expense = Expense::create([
            'user_id' => auth()->id(),
            'expense_category_id' => $validated['expense_category_id'],
            'financial_account_id' => $validated['financial_account_id'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_number' => $validated['receipt_number'],
            'notes' => $validated['notes'],
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);

        if ($productId) {
            $product = Product::findOrFail($productId);
            $product->increment('stock_quantity', $quantity);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'movement_type' => 'in',
                'quantity' => $quantity,
                'reason' => "Compra - Despesa #$expense->id",
                'reference_id' => $expense->id,
                'movement_date' => $validated['expense_date'],
            ]);
        }

        $this->financialService->syncExpenseTransaction($expense);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa registrada com sucesso!',
                'data' => $expense->load(['user', 'category', 'financialAccount']),
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
        // $this->authorize('update-expense', $expense);

        $categories = ExpenseCategory::all();
        $financialAccounts = FinancialAccount::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('type', 'product')->where('is_active', true)->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'financialAccounts', 'products'));
    }

    /**
     * Atualizar despesa
     */
    public function update(Request $request, Expense $expense)
    {
        // Verificar permissão
        // $this->authorize('update-expense', $expense);

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $oldProductId = $expense->product_id;
        $oldQuantity = $expense->quantity ?? 1;
        $newProductId = $request->filled('product_id') ? $request->product_id : null;
        $newQuantity = $request->filled('quantity') ? $request->quantity : 1;

        if ($oldProductId) {
            $oldProduct = Product::find($oldProductId);
            if ($oldProduct) {
                $oldProduct->decrement('stock_quantity', $oldQuantity);
                StockMovement::where('reference_id', $expense->id)
                    ->where('movement_type', 'in')
                    ->delete();
            }
        }

        $expense->update([
            'expense_category_id' => $validated['expense_category_id'],
            'financial_account_id' => $validated['financial_account_id'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_number' => $validated['receipt_number'],
            'notes' => $validated['notes'],
            'product_id' => $newProductId,
            'quantity' => $newQuantity,
        ]);

        if ($newProductId) {
            $newProduct = Product::findOrFail($newProductId);
            $newProduct->increment('stock_quantity', $newQuantity);

            StockMovement::create([
                'product_id' => $newProduct->id,
                'user_id' => auth()->id(),
                'movement_type' => 'in',
                'quantity' => $newQuantity,
                'reason' => "Compra - Despesa #$expense->id",
                'reference_id' => $expense->id,
                'movement_date' => $validated['expense_date'],
            ]);
        }

        $this->financialService->syncExpenseTransaction($expense->fresh());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa atualizada com sucesso!',
                'data' => $expense->fresh()->load(['user', 'category', 'financialAccount']),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remover despesa
     */
    public function destroy(Expense $expense, Request $request)
    {
        // $this->authorize('delete', $expense);

        if ($expense->product_id) {
            $product = Product::find($expense->product_id);
            $quantity = $expense->quantity ?? 1;
            if ($product) {
                $product->decrement('stock_quantity', $quantity);
                StockMovement::where('reference_id', $expense->id)
                    ->where('movement_type', 'in')
                    ->delete();
            }
        }

        $this->financialService->removeTransactionsForReference(Expense::class, $expense->id);
        $expense->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa excluída com sucesso!',
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
