<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DebtController;

Route::get('/', function () {
    return view('welcome');
});

// Perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Estatísticas
    Route::get('/profile/stats', [ProfileController::class, 'stats'])->name('profile.stats');
    Route::get('/profile/performance', [ProfileController::class, 'performance'])->name('profile.performance');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ===== CATEGORIAS =====
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // ===== PRODUTOS =====
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->name('update');
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
        
        // APIs
        Route::get('{product}/edit-data', [ProductController::class, 'editData'])->name('edit-data');
        Route::get('api/categories', [ProductController::class, 'getCategories'])->name('getCategories');
        Route::get('api/products', [ProductController::class, 'getProducts'])->name('getProducts');
    });
    
    // ===== PEDIDOS =====
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        
        // Ações específicas
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('convert-to-sale');
        Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        
        // API para busca de produtos
        Route::get('api/search-products', [OrderController::class, 'searchProducts'])->name('api.search-products');
    });
    
    // ===== VENDAS =====
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::get('/manual-create', [SaleController::class, 'manualCreate'])->name('manual-create');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        
        // Ações específicas
        Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
        Route::get('/{sale}/quick-view', [SaleController::class, 'quickView'])->name('quick-view');
        Route::get('/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
        
        // Relatórios e dashboard
        Route::get('/reports/dashboard', [SaleController::class, 'dashboard'])->name('report');
        Route::get('/reports/export', [SaleController::class, 'export'])->name('export');
        
        // API routes para AJAX
        Route::get('/api/search-products', [SaleController::class, 'searchProducts'])->name('search-products');
    });
    
    // ===== DÍVIDAS =====
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/create', [DebtController::class, 'create'])->name('create');
        Route::post('/', [DebtController::class, 'store'])->name('store');
        Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
        Route::get('/{debt}/edit', [DebtController::class, 'edit'])->name('edit');
        Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
        Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
        
        // Pagamentos
        Route::post('/{debt}/add-payment', [DebtController::class, 'addPayment'])->name('add-payment');
        Route::patch('/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::patch('/{debt}/cancel', [DebtController::class, 'cancel'])->name('cancel');
        
        Route::get('/debts/{debt}/edit-data', function (\App\Models\Debt $debt) {
            return response()->json([
                'success' => true,
                'data' => $debt
            ]);
        })->name('edit-data');
        // Relatórios
        Route::get('/reports/debtors', [DebtController::class, 'debtorsReport'])->name('debtors-report');
        Route::post('/update-overdue-status', [DebtController::class, 'updateOverdueStatus'])->name('update-overdue-status');
        
        // API para busca de clientes
        Route::get('/api/search-customers', [DebtController::class, 'searchCustomers'])->name('api.search-customers');
    });
    
    // ===== DESPESAS =====
    Route::get('/expenses/{expense}/details', [ExpenseController::class, 'showData'])->name('expenses.details');
    Route::resource('expenses', ExpenseController::class);
    
    // ===== MOVIMENTAÇÕES DE ESTOQUE =====
    Route::resource('stock-movements', StockMovementController::class);
    
    // ===== RELATÓRIOS =====
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/daily-sales', [ReportController::class, 'dailySales'])->name('daily-sales');
        Route::get('/monthly-sales', [ReportController::class, 'monthlySales'])->name('monthly-sales');
        Route::get('/sales-by-product', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    });
    
    // ===== USUÁRIOS =====
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';