<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Today's sales
        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        
        // Today's expenses
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        
        // Low stock products
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('type', 'product')
            ->where('is_active', true)
            ->get();
        
        // Recent sales
        $recentSales = Sale::with('user', 'items.product')
            ->latest()
            ->limit(5)
            ->get();
        
        // This month's statistics
        $monthStart = Carbon::now()->startOfMonth();
        $monthSales = Sale::where('sale_date', '>=', $monthStart)->sum('total_amount');
        $monthExpenses = Expense::where('expense_date', '>=', $monthStart)->sum('amount');
        
        return view('dashboard.index', compact(
            'todaySales', 'todayExpenses', 'lowStockProducts',
            'recentSales', 'monthSales', 'monthExpenses'
        ));
    }
}