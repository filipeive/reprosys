<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport; 
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
    
        // Métricas principais com verificações de consistência
        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo);
        
        // Gráficos
        $salesChart = $this->getSalesChartData($dateFrom, $dateTo);
        $paymentMethod = $this->getPaymentMethodData($dateFrom, $dateTo);
        
        // Produtos mais vendidos
        $topProducts = $this->getTopProducts($dateFrom, $dateTo);
        
        // Vendas recentes
        $recentSales = Sale::with(['user', 'items'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->latest()
            ->take(10)
            ->get();
    
        // Tabelas detalhadas baseadas no tipo de relatório
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo);
    
        return view('reports.index', array_merge(
            $metricas,
            [
                'salesChartLabels' => $salesChart['labels'],
                'salesChartData' => $salesChart['data'],
                'paymentMethodLabels' => $paymentMethod['labels'],
                'paymentMethodData' => $paymentMethod['data'],
                'topProducts' => $topProducts,
                'recentSales' => $recentSales,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'reportType' => $reportType
            ],
            $dados
        ));
    }
    public function dailySales(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $sales = Sale::select(
                \DB::raw('DATE(sale_date) as date'),
                \DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($sales);
        }

        return view('reports.daily_sales', compact('sales', 'dateFrom', 'dateTo'));
    }

    public function inventory()
    {
        $products = Product::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        return view('reports.inventory', compact('products'));
    }
    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->get();

        return view('reports.low_stock', compact('products'));
    }
    public function monthlySales()
    {
        $sales = Sale::select(
            DB::raw("DATE_FORMAT(sale_date, '%Y-%m') as month"),
            DB::raw("SUM(total_amount) as total")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

        return view('reports.monthly_sales', compact('sales'));
    }
    public function salesByProduct(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $sales = Product::select('products.name')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.name')
            ->selectRaw('SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.total_price) as total_revenue')
            ->orderByDesc('quantity_sold')
            ->get();

        return view('reports.sales_by_product', compact('sales', 'dateFrom', 'dateTo'));
    }
    private function calcularMetricasPrincipais($dateFrom, $dateTo)
    {
        // Total de vendas (número de transações)
        $totalSales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->count();
        
        // Receita bruta (soma de todas as vendas)
        $totalRevenue = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->sum('total_amount');
        
        // Custo dos produtos vendidos (COGS)
        $costOfGoodsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));
        
        // Total de despesas operacionais
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        
        // Cálculos de margem e lucro
        $grossProfit = $totalRevenue - $costOfGoodsSold; // Lucro bruto
        $netProfit = $grossProfit - $totalExpenses; // Lucro líquido
        
        $grossMargin = $totalRevenue > 0 ? (($grossProfit / $totalRevenue) * 100) : 0;
        $netMargin = $totalRevenue > 0 ? (($netProfit / $totalRevenue) * 100) : 0;
        
        // Ticket médio
        $averageTicket = $totalSales > 0 ? ($totalRevenue / $totalSales) : 0;
        
        // Análise de crescimento (comparação com período anterior)
        $previousPeriodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo));
        $previousDateFrom = Carbon::parse($dateFrom)->subDays($previousPeriodDays + 1)->format('Y-m-d');
        $previousDateTo = Carbon::parse($dateFrom)->subDay()->format('Y-m-d');
        
        $previousRevenue = Sale::whereBetween('sale_date', [$previousDateFrom, $previousDateTo])->sum('total_amount');
        $revenueGrowth = $previousRevenue > 0 ? ((($totalRevenue - $previousRevenue) / $previousRevenue) * 100) : 0;
        
        return [
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'costOfGoodsSold' => $costOfGoodsSold,
            'totalExpenses' => $totalExpenses,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'grossMargin' => $grossMargin,
            'netMargin' => $netMargin,
            'averageTicket' => $averageTicket,
            'revenueGrowth' => $revenueGrowth
        ];
    }

    private function getDadosDetalhados($reportType, $dateFrom, $dateTo)
    {
        $dados = [
            'sales' => collect(),
            'expenses' => collect(),
            'products' => collect()
        ];

        if ($reportType === 'sales' || $reportType === 'all') {
            $dados['sales'] = Sale::with(['user', 'items.product'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->select([
                    'id', 'sale_date', 'customer_name', 'customer_phone', 
                    'total_amount', 'payment_method', 'user_id'
                ])
                ->latest()
                ->get()
                ->map(function ($sale) {
                    // Calcular margem por venda
                    $cost = $sale->items->sum(function ($item) {
                        return $item->quantity * ($item->product->purchase_price ?? 0);
                    });
                    $sale->cost = $cost;
                    $sale->profit = $sale->total_amount - $cost;
                    $sale->margin = $sale->total_amount > 0 ? (($sale->profit / $sale->total_amount) * 100) : 0;
                    return $sale;
                });
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $dados['expenses'] = Expense::with(['user', 'category'])
                ->whereBetween('expense_date', [$dateFrom, $dateTo])
                ->select([
                    'id', 'expense_date', 'description', 'amount', 
                    'receipt_number', 'user_id', 'expense_category_id'
                ])
                ->latest()
                ->get();
        }

        if ($reportType === 'products' || $reportType === 'all') {
            $dados['products'] = Product::with(['category'])
                ->where('is_active', true)
                ->select([
                    'id', 'name', 'type', 'category_id', 'purchase_price', 
                    'selling_price', 'stock_quantity', 'min_stock_level'
                ])
                ->get()
                ->map(function ($product) {
                    // Calcular quantidade vendida no período
                    $salesData = DB::table('sale_items')
                        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->where('sale_items.product_id', $product->id)
                        ->select([
                            DB::raw('SUM(sale_items.quantity) as quantity_sold'),
                            DB::raw('SUM(sale_items.total_price) as revenue_generated')
                        ])
                        ->first();
                    
                    $product->quantity_sold = $salesData->quantity_sold ?? 0;
                    $product->revenue_generated = $salesData->revenue_generated ?? 0;
                    $product->markup = $product->purchase_price > 0 ? 
                        ((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100) : 0;
                    
                    return $product;
                });
        }

        return $dados;
    }

    public function profitLoss(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Receitas
        $salesRevenue = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->sum('total_amount');
        
        // Custos dos produtos vendidos
        $costOfGoodsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        // Lucro bruto
        $grossProfit = $salesRevenue - $costOfGoodsSold;

        // Despesas operacionais por categoria
        $expensesByCategory = Expense::with('category')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy('category.name')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            });

        $totalOperatingExpenses = $expensesByCategory->sum();

        // Lucro operacional
        $operatingProfit = $grossProfit - $totalOperatingExpenses;

        // Margens
        $grossMargin = $salesRevenue > 0 ? (($grossProfit / $salesRevenue) * 100) : 0;
        $operatingMargin = $salesRevenue > 0 ? (($operatingProfit / $salesRevenue) * 100) : 0;

        // Análise por produto
        $productProfitability = Product::select('products.name')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.id', 'products.name', 'products.purchase_price')
            ->selectRaw('
                SUM(sale_items.quantity) as quantity_sold,
                SUM(sale_items.total_price) as revenue,
                SUM(sale_items.quantity * products.purchase_price) as cost,
                SUM(sale_items.total_price - (sale_items.quantity * products.purchase_price)) as profit
            ')
            ->orderByDesc('profit')
            ->get();

        return view('reports.profit_loss', compact(
            'dateFrom', 'dateTo', 'salesRevenue', 'costOfGoodsSold', 
            'grossProfit', 'grossMargin', 'expensesByCategory', 
            'totalOperatingExpenses', 'operatingProfit', 'operatingMargin',
            'productProfitability'
        ));
    }

    private function getSalesChartData($dateFrom, $dateTo)
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        
        $labels = [];
        $data = [];
        $profitData = [];
        
        if ($start->diffInDays($end) > 31) {
            // Agrupar por mês
            $sales = DB::table('sales')
                ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
                ->select([
                    DB::raw('YEAR(sales.sale_date) as year'),
                    DB::raw('MONTH(sales.sale_date) as month'),
                    DB::raw('SUM(sales.total_amount) as revenue'),
                    DB::raw('SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as cost')
                ])
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
                
            foreach ($sales as $sale) {
                $labels[] = Carbon::createFromDate($sale->year, $sale->month, 1)->format('M/Y');
                $data[] = floatval($sale->revenue ?? 0);
                $profitData[] = floatval(($sale->revenue ?? 0) - ($sale->cost ?? 0));
            }
        } else {
            // Agrupar por dia
            $sales = DB::table('sales')
                ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
                ->select([
                    DB::raw('DATE(sales.sale_date) as date'),
                    DB::raw('SUM(sales.total_amount) as revenue'),
                    DB::raw('SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as cost')
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            foreach ($sales as $sale) {
                $labels[] = Carbon::parse($sale->date)->format('d/m');
                $data[] = floatval($sale->revenue ?? 0);
                $profitData[] = floatval(($sale->revenue ?? 0) - ($sale->cost ?? 0));
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'profitData' => $profitData
        ];
    }

    private function getPaymentMethodData($dateFrom, $dateTo)
    {
        $payments = Sale::select('payment_method', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total'))
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method')
            ->get();
        
        $labels = [];
        $countData = [];
        $amountData = [];
        
        $methodNames = [
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            'transfer' => 'Transferência',
            'credit' => 'Crédito'
        ];
        
        foreach ($payments as $payment) {
            $labels[] = $methodNames[$payment->payment_method] ?? $payment->payment_method;
            $countData[] = intval($payment->count);
            $amountData[] = floatval($payment->total);
        }
        
        return [
            'labels' => $labels,
            'data' => $countData,
            'amountData' => $amountData
        ];
    }

    private function getTopProducts($dateFrom, $dateTo)
    {
        return Product::select(
            'products.id',
            'products.name',
            'products.purchase_price',
            'products.selling_price',
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_revenue'),
            DB::raw('SUM(sale_items.quantity * products.purchase_price) as total_cost')
        )
        ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
        ->groupBy('products.id', 'products.name', 'products.purchase_price', 'products.selling_price')
        ->orderBy('total_quantity', 'desc')
        ->take(10)
        ->get()
        ->map(function ($product) {
            $product->profit = $product->total_revenue - $product->total_cost;
            $product->margin = $product->total_revenue > 0 ? 
                (($product->profit / $product->total_revenue) * 100) : 0;
            return $product;
        });
    }

    public function dashboard()
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();
        
        // Métricas do dia
        $todayMetrics = $this->calcularMetricasPrincipais($today, $today);
        
        // Métricas do mês
        $monthMetrics = $this->calcularMetricasPrincipais($thisMonth, $today);

        // Produtos com baixo stock
        $lowStockProducts = Product::where('type', 'product')
            ->where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->with('category')
            ->get();

        // Top produtos do mês
        $topProductsThisMonth = $this->getTopProducts($thisMonth, $today);

        // Vendas dos últimos 7 dias com lucro
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dayMetrics = $this->calcularMetricasPrincipais($date, $date);
            
            $last7Days[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'sales' => $dayMetrics['totalSales'],
                'revenue' => $dayMetrics['totalRevenue'],
                'profit' => $dayMetrics['grossProfit']
            ];
        }

        return view('dashboard.index', compact(
            'todayMetrics',
            'monthMetrics', 
            'lowStockProducts',
            'topProductsThisMonth',
            'last7Days'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
        $format = $request->input('format', 'pdf');

        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo);
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo);

        if ($format === 'excel') {
            return Excel::download(
                new ReportExport($dados['sales'], $dados['expenses'], $dados['products'], 
                               $dateFrom, $dateTo, $reportType, $metricas),
                'relatorio_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf', array_merge($metricas, $dados, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'reportType' => $reportType
        ]));

        return $pdf->download('relatorio_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
    public function exportExcel(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');

        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo);
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo);

        return Excel::download(
            new ReportExport($dados['sales'], $dados['expenses'], $dados['products'], 
                        $dateFrom, $dateTo, $reportType, $metricas),
            'relatorio_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Relatório de Fluxo de Caixa
     */
    public function cashFlow(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Entradas de caixa por método de pagamento
        $cashInflows = [
            'cash' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('payment_method', 'cash')
                ->sum('total_amount'),
            'card' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('payment_method', 'card')
                ->sum('total_amount'),
            'transfer' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('payment_method', 'transfer')
                ->sum('total_amount'),
            'credit' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                ->where('payment_method', 'credit')
                ->sum('total_amount')
        ];

        // Saídas de caixa por categoria
        $cashOutflows = Expense::with('category')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy(function ($expense) {
                return $expense->category->name ?? 'Sem Categoria';
            })
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            });

        $totalInflows = array_sum($cashInflows);
        $totalOutflows = $cashOutflows->sum();
        $netCashFlow = $totalInflows - $totalOutflows;

        // Fluxo diário detalhado
        $dailyCashFlow = [];
        $period = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        while ($period <= $end) {
            $date = $period->format('Y-m-d');
            
            $dailyInflow = Sale::whereDate('sale_date', $date)->sum('total_amount');
            $dailyOutflow = Expense::whereDate('expense_date', $date)->sum('amount');
            
            $dailyCashFlow[] = [
                'date' => $period->format('d/m'),
                'date_full' => $period->format('d/m/Y'),
                'inflow' => $dailyInflow,
                'outflow' => $dailyOutflow,
                'net' => $dailyInflow - $dailyOutflow,
                'sales_count' => Sale::whereDate('sale_date', $date)->count()
            ];
            
            $period->addDay();
        }

        // Projeção de caixa (baseada na média dos últimos dias)
        $averageDailyNet = collect($dailyCashFlow)->avg('net');
        $projections = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $futureDate = Carbon::parse($dateTo)->addDays($i);
            $projections[] = [
                'date' => $futureDate->format('d/m'),
                'projected_net' => $averageDailyNet,
                'accumulated' => $netCashFlow + ($averageDailyNet * $i)
            ];
        }

        return view('reports.cash_flow', compact(
            'dateFrom', 'dateTo', 'cashInflows', 'cashOutflows',
            'totalInflows', 'totalOutflows', 'netCashFlow', 
            'dailyCashFlow', 'projections'
        ));
    }

    /**
     * Análise de Rentabilidade por Cliente
     */
    public function customerProfitability(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $customerAnalysis = Sale::with(['items.product'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->get()
            ->groupBy('customer_name')
            ->map(function ($sales, $customerName) {
                $totalRevenue = $sales->sum('total_amount');
                $totalCost = $sales->flatMap->items->sum(function ($item) {
                    return $item->quantity * ($item->product->purchase_price ?? 0);
                });
                $totalProfit = $totalRevenue - $totalCost;
                $salesCount = $sales->count();
                $averageTicket = $salesCount > 0 ? $totalRevenue / $salesCount : 0;
                
                return [
                    'customer_name' => $customerName,
                    'phone' => $sales->first()->customer_phone,
                    'sales_count' => $salesCount,
                    'total_revenue' => $totalRevenue,
                    'total_cost' => $totalCost,
                    'total_profit' => $totalProfit,
                    'profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
                    'average_ticket' => $averageTicket,
                    'last_purchase' => $sales->sortByDesc('sale_date')->first()->sale_date,
                    'first_purchase' => $sales->sortBy('sale_date')->first()->sale_date
                ];
            })
            ->sortByDesc('total_profit')
            ->values();

        return view('reports.customer_profitability', compact(
            'dateFrom', 'dateTo', 'customerAnalysis'
        ));
    }

    /**
     * Análise ABC de Produtos
     */
    public function abcAnalysis(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Análise de produtos por receita
        $productAnalysis = Product::select('products.*')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.id')
            ->selectRaw('
                products.*,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.total_price) as total_revenue,
                COUNT(DISTINCT sales.id) as sales_transactions
            ')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = $productAnalysis->sum('total_revenue');
        $cumulativeRevenue = 0;

        // Classificação ABC
        $abcProducts = $productAnalysis->map(function ($product) use ($totalRevenue, &$cumulativeRevenue) {
            $cumulativeRevenue += $product->total_revenue;
            $revenuePercentage = $totalRevenue > 0 ? ($product->total_revenue / $totalRevenue) * 100 : 0;
            $cumulativePercentage = $totalRevenue > 0 ? ($cumulativeRevenue / $totalRevenue) * 100 : 0;
            
            // Classificação ABC
            if ($cumulativePercentage <= 80) {
                $classification = 'A';
            } elseif ($cumulativePercentage <= 95) {
                $classification = 'B';
            } else {
                $classification = 'C';
            }

            $product->revenue_percentage = $revenuePercentage;
            $product->cumulative_percentage = $cumulativePercentage;
            $product->abc_classification = $classification;
            
            return $product;
        });

        // Estatísticas por classe
        $abcStats = [
            'A' => $abcProducts->where('abc_classification', 'A'),
            'B' => $abcProducts->where('abc_classification', 'B'),
            'C' => $abcProducts->where('abc_classification', 'C')
        ];

        return view('reports.abc_analysis', compact(
            'dateFrom', 'dateTo', 'abcProducts', 'abcStats', 'totalRevenue'
        ));
    }

    /**
     * Comparativo de Períodos
     */
    public function periodComparison(Request $request)
    {
        $currentDateFrom = $request->input('current_date_from', now()->startOfMonth()->format('Y-m-d'));
        $currentDateTo = $request->input('current_date_to', now()->format('Y-m-d'));
        $previousDateFrom = $request->input('previous_date_from', now()->subMonth()->startOfMonth()->format('Y-m-d'));
        $previousDateTo = $request->input('previous_date_to', now()->subMonth()->endOfMonth()->format('Y-m-d'));

        // Métricas do período atual
        $currentMetrics = $this->calcularMetricasPrincipais($currentDateFrom, $currentDateTo);
        
        // Métricas do período anterior
        $previousMetrics = $this->calcularMetricasPrincipais($previousDateFrom, $previousDateTo);

        // Cálculo de variações
        $comparisons = [];
        $metricsToCompare = [
            'totalSales' => 'Total de Vendas',
            'totalRevenue' => 'Receita Total',
            'costOfGoodsSold' => 'Custo dos Produtos',
            'totalExpenses' => 'Despesas Totais',
            'grossProfit' => 'Lucro Bruto',
            'netProfit' => 'Lucro Líquido',
            'averageTicket' => 'Ticket Médio'
        ];

        foreach ($metricsToCompare as $key => $label) {
            $current = $currentMetrics[$key] ?? 0;
            $previous = $previousMetrics[$key] ?? 0;
            
            $variation = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
            $absolute_variation = $current - $previous;
            
            $comparisons[$key] = [
                'label' => $label,
                'current' => $current,
                'previous' => $previous,
                'variation_percent' => $variation,
                'absolute_variation' => $absolute_variation,
                'trend' => $variation > 0 ? 'up' : ($variation < 0 ? 'down' : 'stable')
            ];
        }

        // Top produtos por período
        $currentTopProducts = $this->getTopProducts($currentDateFrom, $currentDateTo)->take(5);
        $previousTopProducts = $this->getTopProducts($previousDateFrom, $previousDateTo)->take(5);

        return view('reports.period_comparison', compact(
            'currentDateFrom', 'currentDateTo', 'previousDateFrom', 'previousDateTo',
            'currentMetrics', 'previousMetrics', 'comparisons',
            'currentTopProducts', 'previousTopProducts'
        ));
    }

    /**
     * Alertas e Insights Automáticos
     */
    public function businessInsights(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $insights = [];
        $alerts = [];
        $recommendations = [];

        // Calcular métricas
        $metrics = $this->calcularMetricasPrincipais($dateFrom, $dateTo);

        // ALERTAS CRÍTICOS
        if ($metrics['netMargin'] < 5) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Margem Líquida Crítica',
                'message' => 'A margem líquida está abaixo de 5%. Revise custos e preços urgentemente.',
                'value' => number_format($metrics['netMargin'], 1) . '%'
            ];
        }

        if ($metrics['grossMargin'] < 20) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Margem Bruta Baixa',
                'message' => 'A margem bruta está abaixo do recomendado (20%). Considere revisar preços de venda.',
                'value' => number_format($metrics['grossMargin'], 1) . '%'
            ];
        }

        // Produtos com baixo estoque
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Produtos com Estoque Baixo',
                'message' => "Existem {$lowStockCount} produtos com estoque abaixo do mínimo.",
                'value' => $lowStockCount . ' produtos'
            ];
        }

        // INSIGHTS POSITIVOS
        if ($metrics['revenueGrowth'] > 10) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Crescimento Acelerado',
                'message' => 'As vendas estão crescendo consistentemente. Continue investindo nas estratégias atuais.',
                'value' => '+' . number_format($metrics['revenueGrowth'], 1) . '%'
            ];
        }

        if ($metrics['grossMargin'] >= 40) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Excelente Margem Bruta',
                'message' => 'A margem bruta está acima de 40%, indicando boa precificação.',
                'value' => number_format($metrics['grossMargin'], 1) . '%'
            ];
        }

        // RECOMENDAÇÕES
        if ($metrics['averageTicket'] < 100) {
            $recommendations[] = [
                'title' => 'Aumentar Ticket Médio',
                'description' => 'O ticket médio está baixo. Considere estratégias de venda cruzada ou upselling.',
                'action' => 'Treinar equipe em técnicas de vendas',
                'priority' => 'medium'
            ];
        }

        // Análise de sazonalidade
        $monthlySales = Sale::select(
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereYear('sale_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $currentMonth = now()->month;
        $avgMonthlySales = array_sum($monthlySales) / count($monthlySales);
        $currentMonthSales = $monthlySales[$currentMonth] ?? 0;

        if ($currentMonthSales > $avgMonthlySales * 1.2) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Pico Sazonal',
                'message' => 'Este mês está 20% acima da média anual. Aproveite para aumentar estoque.',
                'value' => '+' . number_format((($currentMonthSales / $avgMonthlySales) - 1) * 100, 1) . '%'
            ];
        }

        return view('reports.business_insights', compact(
            'dateFrom', 'dateTo', 'insights', 'alerts', 
            'recommendations', 'metrics'
        ));
    }
}