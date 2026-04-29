<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\UserActivity;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private FinancialService $financialService)
    {
    }

    /**
     * Exibe o dashboard principal.
     * TODOS os cálculos financeiros passam pelo FinancialService centralizado.
     */
    public function index()
    {
        // --- CÁLCULOS DE HOJE ---
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todayOutflows = $this->financialService->sumTransactions($todayStr, $todayStr, 'out');
        $todayProductsSold = Sale::whereDate('sale_date', $today)
            ->withCount('items')
            ->get()
            ->sum('items_count');

        // --- CÁLCULOS DE COMPARAÇÃO (HOJE vs ONTEM) ---
        $yesterday = Carbon::yesterday();
        $yesterdayStr = $yesterday->toDateString();

        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)->sum('total_amount');
        $yesterdayOutflows = $this->financialService->sumTransactions($yesterdayStr, $yesterdayStr, 'out');

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $outflowsChange = $this->calculatePercentageChange($todayOutflows, $yesterdayOutflows);

        // --- CÁLCULOS DO MÊS ATUAL (via FinancialService) ---
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthStartStr = $monthStart->toDateString();
        $monthEndStr = $monthEnd->toDateString();

        $monthSales = Sale::where('sale_date', '>=', $monthStart)->sum('total_amount');
        $monthExpenses = Expense::where('expense_date', '>=', $monthStart)->sum('amount');

        // Use centralized FinancialService for transaction-based metrics
        $monthSummary = $this->financialService->getPeriodSummary($monthStartStr, $monthEndStr);
        $monthReceived = $monthSummary['inflows'];
        $monthOutflows = $monthSummary['outflows'];

        $monthProfit = $monthSales - $monthExpenses;
        $monthCostOfGoods = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.sale_date', '>=', $monthStart)
            ->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));
        $monthGrossProfit = $monthSales - $monthCostOfGoods;
        $monthRealProfit = $monthGrossProfit - $monthExpenses;
        $monthInvestment = $monthCostOfGoods + $monthExpenses;
        $monthRoi = $monthInvestment > 0 ? ($monthRealProfit / $monthInvestment) * 100 : 0;
        $monthGrossMargin = $monthSales > 0 ? ($monthGrossProfit / $monthSales) * 100 : 0;
        $monthNetMargin = $monthSales > 0 ? ($monthRealProfit / $monthSales) * 100 : 0;
        $monthActiveCustomers = Sale::where('sale_date', '>=', $monthStart)
                                    ->distinct('customer_name')
                                    ->count('customer_name');

        // Use centralized FinancialService for capital and receivables
        $currentCapital = $this->financialService->getCurrentCapital();
        $accountsReceivable = $this->financialService->getAccountsReceivable();
        $monthNetCashFlow = $monthReceived - $monthOutflows;

        // --- CÁLCULOS DO MÊS ANTERIOR (PARA COMPARAÇÃO) ---
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $prevMonthStartStr = $prevMonthStart->toDateString();
        $prevMonthEndStr = $prevMonthEnd->toDateString();

        $prevMonthSales = Sale::whereBetween('sale_date', [$prevMonthStart, $prevMonthEnd])->sum('total_amount');
        $prevMonthExpenses = Expense::whereBetween('expense_date', [$prevMonthStart, $prevMonthEnd])->sum('amount');
        $prevMonthSummary = $this->financialService->getPeriodSummary($prevMonthStartStr, $prevMonthEndStr);
        $prevMonthReceived = $prevMonthSummary['inflows'];
        $prevMonthProfit = $prevMonthSales - $prevMonthExpenses;
        $prevMonthCostOfGoods = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$prevMonthStart, $prevMonthEnd])
            ->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));
        $prevMonthRealProfit = ($prevMonthSales - $prevMonthCostOfGoods) - $prevMonthExpenses;

        $monthSalesChange = $this->calculatePercentageChange($monthSales, $prevMonthSales);
        $monthReceivedChange = $this->calculatePercentageChange($monthReceived, $prevMonthReceived);
        $monthProfitChange = $this->calculatePercentageChange($monthRealProfit, $prevMonthRealProfit);

        // --- DADOS DO GRÁFICO (via FinancialService centralizado) ---
        $salesChartData = $this->getSalesChartData();
        $cashFlowChartData = $this->financialService->getCashFlowChartData(7);

        // --- PRODUTOS COM ESTOQUE BAIXO ---
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('type', 'product')
            ->where('is_active', true)
            ->get();
        
        // --- VENDAS RECENTES ---
        $recentSales = Sale::with('user', 'items.product')
            ->latest()
            ->limit(5)
            ->get();
        
        // Extrair variáveis dos arrays para o compact()
        $salesChangePercent = $salesChange['percent'];
        $salesChangeDirection = $salesChange['direction'];
        $salesChangeIcon = $salesChange['icon'];

        $outflowsChangePercent = $outflowsChange['percent'];
        $outflowsChangeDirection = $outflowsChange['direction'];
        $outflowsChangeIcon = $outflowsChange['icon'];

        $monthSalesChangePercent = $monthSalesChange['percent'];
        $monthSalesChangeDirection = $monthSalesChange['direction'];
        $monthSalesChangeIcon = $monthSalesChange['icon'];

        $monthReceivedChangePercent = $monthReceivedChange['percent'];
        $monthReceivedChangeDirection = $monthReceivedChange['direction'];
        $monthReceivedChangeIcon = $monthReceivedChange['icon'];

        $monthProfitChangePercent = $monthProfitChange['percent'];
        $monthProfitChangeDirection = $monthProfitChange['direction'];
        $monthProfitChangeIcon = $monthProfitChange['icon'];

        // --- ALERTAS INTELIGENTES ---
        $this->checkAndSetAlerts($lowStockProducts, $todayOutflows, $todaySales);

        return view('dashboard.index', compact(
            'todaySales', 'todayOutflows', 'lowStockProducts', 'recentSales', 
            'monthSales', 'monthReceived', 'monthOutflows', 'monthExpenses', 'monthProfit', 'todayProductsSold',
            'monthActiveCustomers', 'salesChartData', 'cashFlowChartData',
            'monthCostOfGoods', 'monthGrossProfit', 'monthRealProfit', 'monthRoi',
            'monthGrossMargin', 'monthNetMargin',
            'currentCapital', 'accountsReceivable', 'monthNetCashFlow',
            
            // Dados de Comparação (Hoje)
            'salesChangePercent', 'salesChangeDirection', 'salesChangeIcon',
            'outflowsChangePercent', 'outflowsChangeDirection', 'outflowsChangeIcon',

            // Dados de Comparação (Mês)
            'monthSalesChangePercent', 'monthSalesChangeDirection', 'monthSalesChangeIcon',
            'monthReceivedChangePercent', 'monthReceivedChangeDirection', 'monthReceivedChangeIcon',
            'monthProfitChangePercent', 'monthProfitChangeDirection', 'monthProfitChangeIcon',
            'prevMonthSales', 'prevMonthReceived', 'prevMonthExpenses', 'prevMonthProfit', 'prevMonthRealProfit'
        ));
    }

    /**
     * API para atualizar métricas em tempo real.
     */
    public function apiMetrics()
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todayOutflows = $this->financialService->sumTransactions($todayStr, $todayStr, 'out');
        $lowStockCount = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('type', 'product')
            ->where('is_active', true)
            ->count();
            
        $yesterday = Carbon::yesterday();
        $yesterdayStr = $yesterday->toDateString();
        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)->sum('total_amount');
        $yesterdayOutflows = $this->financialService->sumTransactions($yesterdayStr, $yesterdayStr, 'out');

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $outflowsChange = $this->calculatePercentageChange($todayOutflows, $yesterdayOutflows);
        
        // Gráficos via FinancialService centralizado
        $salesChartData = $this->getSalesChartData();
        $cashFlowChartData = $this->financialService->getCashFlowChartData(7);
        
        $dynamicAlerts = $this->getDynamicAlerts($lowStockCount, $todayOutflows, $todaySales);

        return response()->json([
            'todaySales' => $todaySales,
            'todayOutflows' => $todayOutflows,
            'lowStockCount' => $lowStockCount,
            'activeSales' => Sale::whereDate('sale_date', $today)->count(),
            
            'salesChangePercent' => $salesChange['percent'],
            'salesChangeDirection' => $salesChange['direction'],
            'salesChangeIcon' => $salesChange['icon'],
            
            'outflowsChangePercent' => $outflowsChange['percent'],
            'outflowsChangeDirection' => $outflowsChange['direction'],
            'outflowsChangeIcon' => $outflowsChange['icon'],
            
            'salesChartData' => $salesChartData,
            'cashFlowChartData' => $cashFlowChartData,
            'dynamicAlerts' => $dynamicAlerts,
        ]);
    }

    /**
     * Calcula a mudança percentual e retorna dados para a UI.
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = (($current - $previous) / $previous) * 100;
        }

        $direction = 'neutral';
        $icon = 'fa-minus';

        if ($percent > 0) {
            $direction = 'positive';
            $icon = 'fa-arrow-up';
        } elseif ($percent < 0) {
            $direction = 'negative';
            $icon = 'fa-arrow-down';
        }

        return [
            'percent' => round($percent),
            'direction' => $direction,
            'icon' => $icon,
        ];
    }

    /**
     * Retorna dados dos últimos 7 dias para o gráfico de vendas.
     */
    private function getSalesChartData()
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $sales = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy(DB::raw('DATE(sale_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $expenses = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(expense_date)'))
            ->orderBy(DB::raw('DATE(expense_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $salesData = [];
        $expensesData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');

            $salesData[] = isset($sales[$dateString]) ? (float) $sales[$dateString] : 0.0;
            $expensesData[] = isset($expenses[$dateString]) ? (float) $expenses[$dateString] : 0.0;
        }

        return [
            'labels' => $labels,
            'salesData' => $salesData,
            'expensesData' => $expensesData,
        ];
    }
    
    /**
     * Gera alertas para a API em tempo real (sem usar sessão).
     */
    private function getDynamicAlerts($lowStockCount, $todayOutflows, $todaySales)
    {
        $alerts = [];
        
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockCount} produto(s) estão com estoque baixo!"
            ];
        }

        if ($todayOutflows > 0 && $todaySales > 0 && $todayOutflows > ($todaySales * 0.8)) {
            $alerts[] = [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: Saídas (MT " . number_format($todayOutflows, 2) . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2) . ")!"
            ];
        }

        if ($todaySales > 5000) {
             $alerts[] = [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: Vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2) . "!"
            ];
        }
        
        return $alerts;
    }

    /**
     * Verifica situações críticas e define alertas na sessão.
     */
    private function checkAndSetAlerts($lowStockProducts, $todayOutflows, $todaySales)
    {
        // Alerta de estoque baixo
        if ($lowStockProducts->count() > 0) {
            session()->flash('dashboard_alert', [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockProducts->count()} produto(s) estão com estoque baixo! Verifique a seção de produtos."
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'low_stock_alert',
                'description' => "Alerta de estoque baixo para {$lowStockProducts->count()} produtos",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de despesas altas
        if ($todayOutflows > 0 && $todaySales > 0 && $todayOutflows > ($todaySales * 0.8)) {
            session()->flash('dashboard_alert', [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: As saídas de hoje (MT " . number_format($todayOutflows, 2, ',', '.') . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2, ',', '.') . ")!"
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_expenses_alert',
                'description' => "Alerta de saídas altas: MT " . number_format($todayOutflows, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de bom desempenho
        if ($todaySales > 5000) {
            session()->flash('dashboard_alert', [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: As vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2, ',', '.') . "! Continue assim!"
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_sales_alert',
                'description' => "Alerta de alto desempenho: Vendas de MT " . number_format($todaySales, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
