<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['user', 'category'])
            ->where('user_id', auth()->id());
    
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
    
        $expenses = $query->latest()->paginate(8);
    
        $totalExpenses = (clone $query)->sum('amount');
        $averageExpense = (clone $query)->avg('amount');
        $highestExpense = (clone $query)->orderBy('amount', 'desc')->first();
        $highestExpense = $highestExpense ? $highestExpense->amount : 0;
        $lowestExpense = (clone $query)->orderBy('amount', 'asc')->first();
        $lowestExpense = $lowestExpense ? $lowestExpense->amount : 0;
    
        return view('expenses.index', compact(
            'expenses', 'totalExpenses', 'averageExpense', 'highestExpense', 'lowestExpense'
        ));
    }

    public function create()
    {   
        $categories = ExpenseCategory::all();
        return view('expenses.create', compact('categories') );
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'expense_category_id' => $request->expense_category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt_number' => $request->receipt_number,
            'notes' => $request->notes,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }
    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }
}