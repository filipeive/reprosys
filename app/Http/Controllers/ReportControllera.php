<?php

// App/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportControllera extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function dailySales(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        
        $sales = Sale::with(['user', 'items.product'])
            ->whereDate('sale_date', $date)
            ->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        
        $expenses = Expense::with('user')
            ->whereDate('expense_date', $date)
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalSales - $totalExpenses;
        
        return view('reports.daily-sales', compact(
            'sales', 'expenses', 'date', 'totalSales', 
            'totalTransactions', 'totalExpenses', 'netProfit'
        ));
    }

    public function inventory()
    {
        $products = Product::with('category')
            ->where('type', 'product')
            ->where('is_active', true)
            ->get();
        
        $lowStockProducts = $products->filter(function ($product) {
            return $product->isLowStock();
        });
        
        $totalStockValue = $products->sum(function ($product) {
            return $product->stock_quantity * $product->purchase_price;
        });
        
        return view('reports.inventory', compact(
            'products', 'lowStockProducts', 'totalStockValue'
        ));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->sum('total_amount');
        
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $grossProfit = $sales - $expenses;
        
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        return view('reports.profit-loss', compact(
            'sales', 'expenses', 'grossProfit', 'expensesByCategory',
            'startDate', 'endDate'
        ));
    }
}