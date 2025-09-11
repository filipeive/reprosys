<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Today's sales
        $todaySales = Sale::
        whereDate('sale_date', $today)->sum('total_amount');
        
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

        // ===== ALERTAS INTELIGENTES =====
        $this->checkAndSetAlerts($lowStockProducts, $todayExpenses, $todaySales);

        return view('dashboard.index', compact(
            'todaySales', 'todayExpenses', 'lowStockProducts',
            'recentSales', 'monthSales', 'monthExpenses'
        ));
    }

    /**
     * Verifica situações críticas e define alertas na sessão
     */
    private function checkAndSetAlerts($lowStockProducts, $todayExpenses, $todaySales)
    {
        // Alerta de estoque baixo
        if ($lowStockProducts->count() > 0) {
            session()->flash('dashboard_alert', [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockProducts->count()} produto(s) estão com estoque baixo! Verifique a seção de produtos."
            ]);
            
            // Registrar atividade
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'low_stock_alert',
                'description' => "Alerta de estoque baixo para {$lowStockProducts->count()} produtos",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de despesas altas
        if ($todayExpenses > 0 && $todaySales > 0 && $todayExpenses > ($todaySales * 0.8)) {
            session()->flash('dashboard_alert', [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: As despesas de hoje (MT " . number_format($todayExpenses, 2, ',', '.') . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2, ',', '.') . ")!"
            ]);
            
            // Registrar atividade
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_expenses_alert',
                'description' => "Alerta de despesas altas: MT " . number_format($todayExpenses, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de bom desempenho
        if ($todaySales > 5000) { // Ajuste este valor conforme sua realidade
            session()->flash('dashboard_alert', [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: As vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2, ',', '.') . "! Continue assim!"
            ]);
            
            // Registrar atividade
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_sales_alert',
                'description' => "Alerta de alto desempenho: Vendas de MT " . number_format($todaySales, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
    /**
     * API para atualizar métricas em tempo real
     */
    public function apiMetrics()
    {
        $today = Carbon::today();
        
        return response()->json([
            'todaySales' => Sale::whereDate('sale_date', $today)->sum('total_amount'),
            'todayExpenses' => Expense::whereDate('expense_date', $today)->sum('amount'),
            'lowStockCount' => Product::whereRaw('stock_quantity <= min_stock_level')
                ->where('type', 'product')
                ->where('is_active', true)
                ->count(),
            'activeSales' => Sale::whereDate('sale_date', $today)->count(),
        ]);
    }
}