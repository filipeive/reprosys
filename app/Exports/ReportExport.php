<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReportExport implements FromView
{
    protected $sales, $expenses, $products, $dateFrom, $dateTo, $reportType, $totalSales, $totalRevenue, $totalExpenses;

    public function __construct($sales, $expenses, $products, $dateFrom, $dateTo, $reportType, $totalSales, $totalRevenue, $totalExpenses)
    {
        $this->sales = $sales;
        $this->expenses = $expenses;
        $this->products = $products;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportType = $reportType;
        $this->totalSales = $totalSales;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpenses = $totalExpenses;
    }

    public function view(): View
    {
        return view('reports.excel', [
            'sales' => $this->sales,
            'expenses' => $this->expenses,
            'products' => $this->products,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'reportType' => $this->reportType,
            'totalSales' => $this->totalSales,
            'totalRevenue' => $this->totalRevenue,
            'totalExpenses' => $this->totalExpenses,
        ]);
    }
}