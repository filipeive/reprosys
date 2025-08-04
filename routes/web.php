<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //Route::resource('categories', CategoryController::class);
    // Rotas para categorias
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });
    Route::prefix('products')->name('products.')->group(function () {
    Route::get('categories', [ProductController::class, 'getCategories'])->name('getCategories');
    Route::get('getProducts', [ProductController::class, 'getProducts'])->name('getProducts');
    Route::post('{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
    Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('{product}', [ProductController::class, 'update'])->name('update');
    Route::get('{product}', [ProductController::class, 'show'])->name('show');
    });
    // Rotas para vendas
    //Route::resource('sales', SaleController::class);
    // Rotas para vendas
    Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SaleController::class, 'index'])->name('index');
    Route::get('/create', [SaleController::class, 'create'])->name('create');
    Route::get('/manual-create', [SaleController::class, 'manualCreate'])->name('manual-create');
    Route::post('/', [SaleController::class, 'store'])->name('store');
    Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
    Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
    Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
    Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
    Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
    Route::get('/{sale}/quick-view', [SaleController::class, 'quickView'])->name('quick-view');
    Route::get('/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('duplicate');
    Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
    // Rotas para relatórios e dashboard
    Route::get('/reports/dashboard', [SaleController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/export', [SaleController::class, 'export'])->name('export');
    // API routes para AJAX
    Route::get('/api/search-products', [SaleController::class, 'searchProducts'])->name('search-products');
    });
    
    // Rotas para despesas
    Route::resource('expenses', ExpenseController::class);
    Route::resource('stock-movements', StockMovementController::class);
    // Página principal de relatórios
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('can:admin');
    // Exportar relatório geral em PDF
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    // Exportar relatório geral em Excel (requer Laravel Excel)
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    // Vendas diárias
    Route::get('reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily-sales');
    // Vendas mensais (por mês)
    Route::get('reports/monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.monthly-sales');
    // Vendas por produto
    Route::get('reports/sales-by-product', [ReportController::class, 'salesByProduct'])->name('reports.sales-by-product');
    // Lucros e prejuízos
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    // Inventário de produtos
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    // Produtos com baixo estoque
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::resource('users', UserController::class);
}); 
