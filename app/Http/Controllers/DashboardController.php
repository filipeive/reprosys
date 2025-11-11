<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necessário para cálculos mais complexos

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard principal.
     */
    /**
     * Exibe o dashboard principal.
     */
    public function index()
    {
        // --- CÁLCULOS DE HOJE ---
        $today = Carbon::today();
        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $todayProductsSold = Sale::whereDate('sale_date', $today)
            ->withCount('items') // Assumindo que 'items' é a relação
            ->get()
            ->sum('items_count');

        // --- CÁLCULOS DE COMPARAÇÃO (HOJE vs ONTEM) ---
        $yesterday = Carbon::yesterday();
        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)->sum('total_amount');
        $yesterdayExpenses = Expense::whereDate('expense_date', $yesterday)->sum('amount');

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $expensesChange = $this->calculatePercentageChange($todayExpenses, $yesterdayExpenses);

        // --- CÁLCULOS DO MÊS ATUAL ---
        $monthStart = Carbon::now()->startOfMonth();
        $monthSales = Sale::where('sale_date', '>=', $monthStart)->sum('total_amount');
        $monthExpenses = Expense::where('expense_date', '>=', $monthStart)->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;
        $monthActiveCustomers = Sale::where('sale_date', '>=', $monthStart)
                                    ->distinct('customer_name')
                                    ->count('customer_name');

        // --- CÁLCULOS DO MÊS ANTERIOR (PARA COMPARAÇÃO) ---
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $prevMonthSales = Sale::whereBetween('sale_date', [$prevMonthStart, $prevMonthEnd])->sum('total_amount');
        $prevMonthExpenses = Expense::whereBetween('expense_date', [$prevMonthStart, $prevMonthEnd])->sum('amount');
        $prevMonthProfit = $prevMonthSales - $prevMonthExpenses;

        $monthSalesChange = $this->calculatePercentageChange($monthSales, $prevMonthSales);
        $monthProfitChange = $this->calculatePercentageChange($monthProfit, $prevMonthProfit);

        // --- DADOS DO GRÁFICO (ÚLTIMOS 7 DIAS) ---
        $salesChartData = $this->getSalesChartData();
        $cashFlowChartData = $this->getCashFlowChartData();

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
        
        // ===== INÍCIO DA CORREÇÃO =====
        // Extrair variáveis dos arrays para o compact()
        $salesChangePercent = $salesChange['percent'];
        $salesChangeDirection = $salesChange['direction'];
        $salesChangeIcon = $salesChange['icon'];

        $expensesChangePercent = $expensesChange['percent'];
        $expensesChangeDirection = $expensesChange['direction'];
        $expensesChangeIcon = $expensesChange['icon'];

        $monthSalesChangePercent = $monthSalesChange['percent'];
        $monthSalesChangeDirection = $monthSalesChange['direction'];
        $monthSalesChangeIcon = $monthSalesChange['icon'];

        $monthProfitChangePercent = $monthProfitChange['percent'];
        $monthProfitChangeDirection = $monthProfitChange['direction'];
        $monthProfitChangeIcon = $monthProfitChange['icon'];
        // ===== FIM DA CORREÇÃO =====

        // --- ALERTAS INTELIGENTES (para a carga da página) ---
        $this->checkAndSetAlerts($lowStockProducts, $todayExpenses, $todaySales);

        return view('dashboard.index', compact(
            'todaySales', 'todayExpenses', 'lowStockProducts', 'recentSales', 
            'monthSales', 'monthExpenses', 'monthProfit', 'todayProductsSold',
            'monthActiveCustomers', 'salesChartData', 'cashFlowChartData',
            
            // Dados de Comparação (Hoje) - Agora definidos
            'salesChangePercent', 'salesChangeDirection', 'salesChangeIcon',
            'expensesChangePercent', 'expensesChangeDirection', 'expensesChangeIcon',

            // Dados de Comparação (Mês) - Agora definidos
            'monthSalesChangePercent', 'monthSalesChangeDirection', 'monthSalesChangeIcon',
            'monthProfitChangePercent', 'monthProfitChangeDirection', 'monthProfitChangeIcon',
            'prevMonthSales', 'prevMonthExpenses', 'prevMonthProfit'
        ));
    }

    /**
     * API para atualizar métricas em tempo real.
     * AGORA TAMBÉM RETORNA DADOS DE COMPARAÇÃO E GRÁFICO!
     */
    public function apiMetrics()
    {
        // Cálculos de Hoje
        $today = Carbon::today();
        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $lowStockCount = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('type', 'product')
            ->where('is_active', true)
            ->count();
            
        // Cálculos de Ontem (para comparação em tempo real)
        $yesterday = Carbon::yesterday();
        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)->sum('total_amount');
        $yesterdayExpenses = Expense::whereDate('expense_date', $yesterday)->sum('amount');

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $expensesChange = $this->calculatePercentageChange($todayExpenses, $yesterdayExpenses);
        
        // Dados do Gráfico (para atualização em tempo real)
        $salesChartData = $this->getSalesChartData();
        $cashFlowChartData = $this->getCashFlowChartData();
        
        // Alertas Dinâmicos (para Toasts)
        $dynamicAlerts = $this->getDynamicAlerts($lowStockCount, $todayExpenses, $todaySales);

        return response()->json([
            'todaySales' => $todaySales,
            'todayExpenses' => $todayExpenses,
            'lowStockCount' => $lowStockCount,
            'activeSales' => Sale::whereDate('sale_date', $today)->count(),
            
            // Novos dados para UI dinâmica
            'salesChangePercent' => $salesChange['percent'],
            'salesChangeDirection' => $salesChange['direction'],
            'salesChangeIcon' => $salesChange['icon'],
            
            'expensesChangePercent' => $expensesChange['percent'],
            'expensesChangeDirection' => $expensesChange['direction'],
            'expensesChangeIcon' => $expensesChange['icon'],
            
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
     * Retorna dados dos últimos 7 dias para o gráfico.
     */
    private function getSalesChartData()
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();
        // 1. Obter vendas agrupadas por dia (garantindo uso de DATE() no groupBy)
        $sales = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy(DB::raw('DATE(sale_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        // 2. Obter despesas agrupadas por dia
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

        // 3. Iterar pelos últimos 7 dias para preencher os arrays (garante 7 pontos diários)
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d/m'); // Formato "27/10"

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
     * @return array
     */
    private function getDynamicAlerts($lowStockCount, $todayExpenses, $todaySales)
    {
        $alerts = [];
        
        // Alerta de estoque baixo (se for a primeira vez que vê)
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockCount} produto(s) estão com estoque baixo!"
            ];
        }

        // Alerta de despesas altas
        if ($todayExpenses > 0 && $todaySales > 0 && $todayExpenses > ($todaySales * 0.8)) {
            $alerts[] = [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: Despesas (MT " . number_format($todayExpenses, 2) . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2) . ")!"
            ];
        }

        // Alerta de bom desempenho
        if ($todaySales > 5000) { // Ajuste este valor
             $alerts[] = [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: Vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2) . "!"
            ];
        }
        
        return $alerts;
    }

    /**
     * Verifica situações críticas e define alertas na sessão (para carga da página).
     * (Mantive sua lógica original de sessão e UserActivity)
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
     * Retorna dados de fluxo de caixa (entradas, saídas, fluxo líquido) para os últimos 7 dias.
     * Ideal para o gráfico de cash flow no dashboard.
     */
    private function getCashFlowChartData()
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        // 1. Obter vendas (entradas) agrupadas por dia
        $inflows = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy(DB::raw('DATE(sale_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        // 2. Obter despesas (saídas) agrupadas por dia
        $outflows = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(expense_date)'))
            ->orderBy(DB::raw('DATE(expense_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $inflowsData = [];
        $outflowsData = [];
        $netFlowData = [];

        // 3. Iterar pelos últimos 7 dias para preencher os arrays
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d/m'); // Formato "27/10"

            $inflow = isset($inflows[$dateString]) ? (float) $inflows[$dateString] : 0.0;
            $outflow = isset($outflows[$dateString]) ? (float) $outflows[$dateString] : 0.0;
            $netFlow = $inflow - $outflow;

            $inflowsData[] = $inflow;
            $outflowsData[] = $outflow;
            $netFlowData[] = $netFlow;
        }

        return [
            'labels' => $labels,
            'inflowsData' => $inflowsData,
            'outflowsData' => $outflowsData,
            'netFlowData' => $netFlowData,
        ];
    }
}