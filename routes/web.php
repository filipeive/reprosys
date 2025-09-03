<?php
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
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
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

// ===== AUTHENTICATION ROUTES =====
Auth::routes(['register' => false]); // Disable default register route

// Registro protegido com senha administrativa
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/verify-admin', [RegisterController::class, 'verifyAdminPasswordAjax'])->name('register.verify-admin');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ===== PROTECTED ROUTES =====
Route::middleware(['auth', 'permissions'])->group(function () {
    // Dashboard - Acesso para todos os usuários logados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ===== PERFIL DO USUÁRIO =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
        Route::get('/performance', [ProfileController::class, 'performance'])->name('performance');
        Route::get('/show', [ProfileController::class, 'show'])->name('show');
    });
    
    // ===== PONTO DE VENDA - Acesso para todos =====
    Route::get('/sales/create', [SaleController::class, 'create'])
        ->name('sales.create');
    
    // ===== PRODUTOS - Permissões diferenciadas =====
    Route::prefix('products')->name('products.')->group(function () {
        // Visualizar produtos - todos podem
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        
        // Criar produtos - admin e manager
        Route::middleware('permissions:create_products')->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
        });
        
        // Editar produtos - admin, manager e staff
        Route::middleware('permissions:edit_products')->group(function () {
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::get('/{product}/edit-data', [ProductController::class, 'editData'])->name('edit-data');
        });
        
        // Deletar produtos - apenas admin
        Route::middleware('permissions:delete_products')->group(function () {
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });
        
        // Ajustar estoque - admin, manager e staff
        Route::middleware('permissions:adjust_stock')->group(function () {
            Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
        });
        
        // APIs
        Route::get('/api/categories', [ProductController::class, 'getCategories'])->name('getCategories');
        Route::get('/api/products', [ProductController::class, 'getProducts'])->name('getProducts');
    });
    
    // ===== CATEGORIAS - Apenas admin pode gerenciar =====
    Route::prefix('categories')->name('categories.')->middleware('permissions:manage_categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // ===== PEDIDOS =====
    Route::prefix('orders')->name('orders.')->group(function () {
        // Visualizar e criar pedidos - todos podem
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/api/search-products', [OrderController::class, 'searchProducts'])->name('api.search-products');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/details', [OrderController::class, 'showDetails'])->name('details');
        
        // Editar pedidos - verificação de propriedade
        Route::middleware('permissions:edit_orders')->group(function () {
            Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{order}', [OrderController::class, 'update'])->name('update');
            Route::get('/{order}/edit-data', [OrderController::class, 'editData'])->name('edit-data');
            Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        });
        
        // Converter para venda - admin e manager
        Route::middleware('permissions:convert_orders')->group(function () {
            Route::post('/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('convert-to-sale');
        });
        
        // Deletar pedidos - apenas admin
        Route::middleware('permissions:delete_orders')->group(function () {
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        });
        
        // Duplicar e relatórios
        Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        Route::get('/reports/orders', [OrderController::class, 'report'])->name('report');
    });
    
    // ===== VENDAS =====
    Route::prefix('sales')->name('sales.')->group(function () {
        // Operações básicas - todos podem
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/manual-create', [SaleController::class, 'manualCreate'])->name('manual-create');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
        Route::get('/{sale}/quick-view', [SaleController::class, 'quickView'])->name('quick-view');
        Route::get('/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('duplicate');
        Route::get('/api/search-products', [SaleController::class, 'searchProducts'])->name('search-products');
        
        // Editar vendas - verificação de propriedade ou permissão admin
        Route::middleware('permissions:edit_sales')->group(function () {
            Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
            Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
            Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
        });
        
        // Deletar vendas - apenas admin
        Route::middleware('permissions:delete_sales')->group(function () {
            Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        });
        
        // Relatórios - admin e manager
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/dashboard', [SaleController::class, 'dashboard'])->name('report');
            Route::get('/reports/export', [SaleController::class, 'export'])->name('export');
        });
    });
    
    // ===== DÍVIDAS =====
    Route::prefix('debts')->name('debts.')->group(function () {
        // Operações básicas - todos podem
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/create', [DebtController::class, 'create'])->name('create');
        Route::post('/', [DebtController::class, 'store'])->name('store');
        Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
        Route::get('/{debt}/details', [DebtController::class, 'showDetails'])->name('details');
        
        // Pagamentos - todos podem receber
        Route::post('/{debt}/add-payment', [DebtController::class, 'addPayment'])->name('add-payment');
        Route::patch('/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('mark-as-paid');
        
        // Editar dívidas - verificação de propriedade
        Route::middleware('permissions:edit_debts')->group(function () {
            Route::get('/{debt}/edit', [DebtController::class, 'edit'])->name('edit');
            Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
            Route::get('/{debt}/edit-data', [DebtController::class, 'editData'])->name('edit-data');
        });
        
        // Cancelar/deletar dívidas - admin e manager
        Route::middleware('permissions:delete_debts')->group(function () {
            Route::patch('/{debt}/cancel', [DebtController::class, 'cancel'])->name('cancel');
            Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
        });
        
        // Relatórios
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/debtors', [DebtController::class, 'debtorsReport'])->name('debtors-report');
        });
        
        Route::post('/update-overdue-status', [DebtController::class, 'updateOverdueStatus'])->name('update-overdue-status');
    });
    
    // ===== DESPESAS =====
    Route::prefix('expenses')->name('expenses.')->group(function () {
        // Visualizar - todos podem
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/details', [ExpenseController::class, 'showData'])->name('details');
        
        // Criar - todos podem
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        
        // Editar - admin e manager
        Route::middleware('permissions:edit_expenses')->group(function () {
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        });
        
        // Deletar - apenas admin
        Route::middleware('permissions:delete_expenses')->group(function () {
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ===== MOVIMENTAÇÕES DE ESTOQUE =====
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        // Visualizar - todos podem
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
        
        // Criar - admin e manager
        Route::middleware('permissions:create_stock_movements')->group(function () {
            Route::get('/create', [StockMovementController::class, 'create'])->name('create');
            Route::post('/', [StockMovementController::class, 'store'])->name('store');
        });
        
        // Editar/Deletar - apenas admin
        Route::middleware('permissions:manage_stock')->group(function () {
            Route::get('/{stockMovement}/edit', [StockMovementController::class, 'edit'])->name('edit');
            Route::put('/{stockMovement}', [StockMovementController::class, 'update'])->name('update');
            Route::delete('/{stockMovement}', [StockMovementController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ===== RELATÓRIOS =====
    Route::prefix('reports')->name('reports.')->group(function () {
        // Relatórios básicos - todos podem ver alguns
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily-sales', [ReportController::class, 'dailySales'])->name('daily-sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
        
        // Relatórios avançados - admin e manager
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/monthly-sales', [ReportController::class, 'monthlySales'])->name('monthly-sales');
            Route::get('/sales-by-product', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        });
        
        // Exportar relatórios - admin e manager
        Route::middleware('permissions:export_reports')->group(function () {
            Route::get('/export', [ReportController::class, 'export'])->name('export');
            Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        });
    });
    
    // ===== USUÁRIOS - Apenas admin =====
    Route::prefix('users')->name('users.')->middleware('permissions:manage_users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/details', [UserController::class, 'showDetails'])->name('details');
        Route::get('/{user}/edit-data', [UserController::class, 'editData'])->name('edit-data');
    });
    
    // ===== API ROUTES =====
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/products/available', [DebtController::class, 'getAvailableProducts'])->name('products.available');
        Route::get('/debts/search-customers', [DebtController::class, 'searchCustomers'])->name('debts.search-customers');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll']);
    });
});

require __DIR__.'/auth.php';