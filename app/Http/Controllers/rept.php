<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport; 
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportControzmller extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
    
        // Dados gerais
        $totalSales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->count();
        $totalRevenue = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->sum('total_amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
    
        // Gráfico de vendas
        $salesChart = $this->getSalesChartData($dateFrom, $dateTo);
        $salesChartLabels = $salesChart['labels'];
        $salesChartData = $salesChart['data'];
    
        // Gráfico de métodos de pagamento
        $paymentMethod = $this->getPaymentMethodData($dateFrom, $dateTo);
        $paymentMethodLabels = $paymentMethod['labels'];
        $paymentMethodData = $paymentMethod['data'];
    
        // Produtos mais vendidos
        $topProducts = $this->getTopProducts($dateFrom, $dateTo);
    
        // Vendas recentes
        $recentSales = Sale::with('user')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->latest()
            ->take(10)
            ->get();
    
        // Tabelas detalhadas
        $sales = collect();
        $expenses = collect();
        $products = collect();
    
        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = Sale::with(['user', 'items.product'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->latest()
                ->get();
        }
    
        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = Expense::with('user')
                ->whereBetween('expense_date', [$dateFrom, $dateTo])
                ->latest()
                ->get();
        }
    
        if ($reportType === 'products' || $reportType === 'all') {
            $products = Product::with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
    
        return view('reports.index', compact(
            'totalSales',
            'totalRevenue',
            'totalExpenses',
            'salesChartLabels',
            'salesChartData',
            'paymentMethodLabels',
            'paymentMethodData',
            'topProducts',
            'recentSales',
            'sales',
            'expenses',
            'products'
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
    private function getSalesChartData($dateFrom, $dateTo)
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        
        $labels = [];
        $data = [];
        
        // Se o período for maior que 31 dias, agrupar por mês
        if ($start->diffInDays($end) > 31) {
            $sales = Sale::select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
            foreach ($sales as $sale) {
                $labels[] = Carbon::createFromDate($sale->year, $sale->month, 1)->format('M/Y');
                $data[] = floatval($sale->total);
            }
        } else {
            // Agrupar por dia
            $sales = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
            foreach ($sales as $sale) {
                $labels[] = Carbon::parse($sale->date)->format('d/m');
                $data[] = floatval($sale->total);
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getPaymentMethodData($dateFrom, $dateTo)
    {
        $payments = Sale::select('payment_method', DB::raw('COUNT(*) as count'))
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method')
            ->get();
        
        $labels = [];
        $data = [];
        
        $methodNames = [
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            'transfer' => 'Transferência',
            'credit' => 'Crédito'
        ];
        
        foreach ($payments as $payment) {
            $labels[] = $methodNames[$payment->payment_method] ?? $payment->payment_method;
            $data[] = intval($payment->count);
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getTopProducts($dateFrom, $dateTo)
    {
        return Product::select(
            'products.id',
            'products.name',
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_revenue')
        )
        ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
        ->groupBy('products.id', 'products.name')
        ->orderBy('total_quantity', 'desc')
        ->take(10)
        ->get();
    }
    public function inventory()
    {
        $products = Product::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        return view('reports.inventory', compact('products'));
    }
    public function profitLoss(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $sales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->get();
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->get();

        $revenue = $sales->sum('total_amount');
        $costOfGoodsSold = $sales->flatMap->items->sum(function ($item) {
            return $item->product->purchase_price * $item->quantity;
        });
        $totalExpenses = $expenses->sum('amount');
        $profit = $revenue - ($costOfGoodsSold + $totalExpenses);

        return view('reports.profit_loss', compact(
            'dateFrom', 'dateTo', 'revenue', 'costOfGoodsSold', 'totalExpenses', 'profit'
        ));
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

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
        $format = $request->input('format', 'pdf'); // novo parâmetro

        // Dados para o relatório
        $totalSales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->count();
        $totalRevenue = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->sum('total_amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');

        $sales = collect();
        $expenses = collect();
        $products = collect();

        if ($reportType === 'sales' || $reportType === 'all') {
            $sales = Sale::with(['user', 'items.product'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->latest()
                ->get();
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $expenses = Expense::with('user')
                ->whereBetween('expense_date', [$dateFrom, $dateTo])
                ->latest()
                ->get();
        }

        if ($reportType === 'products' || $reportType === 'all') {
            $products = Product::with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ReportExport($sales, $expenses, $products, $dateFrom, $dateTo, $reportType, $totalSales, $totalRevenue, $totalExpenses),
                'relatorio_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        // PDF padrão
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf', compact(
            'dateFrom',
            'dateTo',
            'reportType',
            'totalSales',
            'totalRevenue',
            'totalExpenses',
            'sales',
            'expenses',
            'products'
        ));

        return $pdf->download('relatorio_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
    public function dashboard()
    {
        // Dados para o dashboard
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();
        $lastMonth = now()->subMonth()->startOfMonth()->toDateString();

        // Vendas de hoje
        $todaySales = Sale::whereDate('sale_date', $today)->count();
        $todayRevenue = Sale::whereDate('sale_date', $today)->sum('total_amount');

        // Vendas do mês
        $monthSales = Sale::whereBetween('sale_date', [$thisMonth, $today])->count();
        $monthRevenue = Sale::whereBetween('sale_date', [$thisMonth, $today])->sum('total_amount');

        // Produtos com baixo stock
        $lowStockProducts = Product::where('type', 'product')
            ->where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->with('category')
            ->get();

        // Produtos mais vendidos este mês
        $topProductsThisMonth = $this->getTopProducts($thisMonth, $today);

        // Vendas dos últimos 7 dias
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $salesCount = Sale::whereDate('sale_date', $date)->count();
            $salesRevenue = Sale::whereDate('sale_date', $date)->sum('total_amount');
            $last7Days[] = [
                'date' => \Carbon\Carbon::parse($date)->format('d/m'),
                'sales' => $salesCount,
                'revenue' => $salesRevenue
            ];
        }

        return view('dashboard.index', compact(
            'todaySales',
            'todayRevenue',
            'monthSales',
            'monthRevenue',
            'lowStockProducts',
            'topProductsThisMonth',
            'last7Days'
        ));
    }
}
