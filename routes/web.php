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
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PasswordChangeController;



Route::get('/', function () {
    return redirect()->route('login');
});

// Registro protegido com senha administrativa
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/verify-admin', [RegisterController::class, 'verifyAdminPasswordAjax'])->name('register.verify-admin');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== PROTECTED ROUTES =====
Route::middleware(['auth', 'permissions', 'temp.password', 'verified'])->group(function () {
    // Dashboard - Acesso para todos os usuários logados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
   // Rotas para troca de senha temporária (apenas para usuários autenticados)
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.update');
    Route::post('/password/skip', [PasswordChangeController::class, 'skip'])->name('password.skip');

    Route::middleware(['auth', 'verified'])->group(function () {
    // ===== BUSCA =====    
    // Busca completa com página de resultados
        Route::get('/search', [SearchController::class, 'index'])->name('search.index');
        
        // API de busca rápida para autocomplete
        Route::get('/api/search', [SearchController::class, 'api'])->name('search.api');
        
        // Busca específica por tipo
        Route::get('/search/{type}', [SearchController::class, 'index'])->name('search.type');
        
    });
    // ===== PERFIL DO USUÁRIO =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
        Route::get('/performance', [ProfileController::class, 'performance'])->name('performance');
        Route::get('/show', [ProfileController::class, 'show'])->name('show');
        //update-photo
        Route::patch('/photo', [ProfileController::class, 'updatePhoto'])->name('update-photo');

    });
    
    // ===== PONTO DE VENDA - create_sales permission =====
    Route::middleware('permissions:create_sales')->group(function () {
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    });
    
    // ===== PRODUTOS - Permissões ajustadas =====
    Route::prefix('products')->name('products.')->group(function () {
        // Relatório e exportação - view_products permission
      
        // Criar produtos - create_products permission
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');

        // Editar produtos - edit_products permission
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
            Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
            Route::post('/bulk-toggle', [ProductController::class, 'bulkToggle'])->name('bulk-toggle');
            Route::middleware('permissions:view_products')->group(function () {
            Route::get('/report', [ProductController::class, 'report'])->name('report');
            Route::get('/export/{format}', [ProductController::class, 'exportProducts'])->name('export');
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
        });

        // Deletar produtos - delete_products permission
        Route::middleware('permissions:delete_products')->group(function () {
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });
    });

    // ===== CATEGORIAS - manage_categories permission =====
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
        // Visualizar pedidos - todos podem ver
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/details', [OrderController::class, 'showDetails'])->name('details');
        Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        
        // Criar pedidos - create_orders permission
        Route::middleware('permissions:create_orders')->group(function () {
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/api/search-products', [OrderController::class, 'searchProducts'])->name('api.search-products');
        });
        
        // Editar pedidos - edit_orders permission
        Route::middleware('permissions:edit_orders')->group(function () {
            Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{order}', [OrderController::class, 'update'])->name('update');
            Route::get('/{order}/edit-data', [OrderController::class, 'editData'])->name('edit-data');
            Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        });
        
        // Converter para venda - convert_orders permission
        Route::middleware('permissions:convert_orders')->group(function () {
            Route::post('/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('convert-to-sale');
        });
        
        // Deletar pedidos - delete_orders permission
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        
        // Relatórios - view_reports permission
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/orders', [OrderController::class, 'report'])->name('report');
        });
    });
    // ===== VENDAS =====
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::middleware('permissions:create_sales')->group(function () {
            Route::get('/manual-create', [SaleController::class, 'manualCreate'])->name('manual-create');
        });
        // Visualizar vendas - view_sales permission
        Route::middleware('permissions:view_sales')->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
            Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
            Route::get('/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('duplicate');

            // APIs também protegidas por permissão de visualização de vendas
            Route::prefix('api/sales')->name('api.')->group(function () {
                Route::get('/{sale}/quick-view', [SaleController::class, 'quickView'])->name('quick-view');
            });
        });

        // Criar vendas - create_sales permission
        Route::middleware('permissions:create_sales')->group(function () {
            Route::get('/create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('/api/search-products', [SaleController::class, 'searchProducts'])->name('search-products');
            
            // Route to create a new debt from a sale
            Route::post('/{sale}/create-debt', [SaleController::class, 'createDebt'])->name('create-debt');
        });

        // Editar vendas - edit_sales permission
        Route::middleware('permissions:edit_sales')->group(function () {
            Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
            Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
            Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
        });

        // Deletar vendas - delete_sales permission
        Route::middleware('permissions:delete_sales')->group(function () {
            Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        });

        // Relatórios - view_reports permission
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/dashboard', [SaleController::class, 'dashboard'])->name('reports');
            Route::get('/reports/export', [SaleController::class, 'export'])->name('export');
        });

        // Exportação de vendas - permissão específica
        Route::middleware('permissions:view_sales')->group(function () {
            Route::get('/export/{format}', [SaleController::class, 'exportSales'])->name('export-data');
        });
            
            // ===== GESTÃO DE DESCONTOS - Permissão edit_sales =====
            Route::middleware('permissions:edit_sales')->group(function () {
                // Aplicar desconto a uma venda
                Route::post('/{sale}/discount/apply', [SaleController::class, 'applyDiscount'])
                    ->name('discount.apply');
                    
                // Remover desconto de uma venda
                Route::delete('/{sale}/discount/remove', [SaleController::class, 'removeDiscount'])
                    ->name('discount.remove');
                    
                // Aplicar desconto a item específico
                Route::post('/{sale}/items/{item}/discount', [SaleController::class, 'applyItemDiscount'])
                    ->name('item.discount.apply');
                    
                // Remover desconto de item específico  
                Route::delete('/{sale}/items/{item}/discount', [SaleController::class, 'removeItemDiscount'])
                    ->name('item.discount.remove');
            });

            // ===== RELATÓRIOS DE DESCONTO - Permissão view_reports =====
            Route::middleware('permissions:view_reports')->group(function () {
                // Relatório de descontos aplicados
                Route::get('/reports/discounts', [SaleController::class, 'discountReport'])
                    ->name('reports.discounts');
                    
                // Análise de impacto de descontos
                Route::get('/reports/discount-impact', [SaleController::class, 'discountImpactReport'])
                    ->name('reports.discount-impact');
                    
                // Exportar relatório de descontos
                Route::get('/reports/discounts/export/{format}', [SaleController::class, 'exportDiscountReport'])
                    ->name('reports.discounts.export');
            });

            // ===== APIs PARA DESCONTOS - Permissão view_sales =====
            Route::middleware('permissions:view_sales')->group(function () {
                Route::prefix('api')->name('api.')->group(function () {
                    // Estatísticas de desconto em tempo real
                    Route::get('/discount-stats', [SaleController::class, 'getDiscountStats'])
                        ->name('discount-stats');
                        
                    // Verificar se desconto pode ser aplicado
                    Route::post('/discount/validate', [SaleController::class, 'validateDiscount'])
                        ->name('discount.validate');
                });
            });
        });
    
    // ===== DÍVIDAS =====
    Route::prefix('debts')->name('debts.')->group(function () {
        // Visualizar dívidas - todos podem
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
        Route::get('/{debt}/details', [DebtController::class, 'showDetails'])->name('details');
        
        // Criar dívidas - create_debts permission
        Route::middleware('permissions:create_debts')->group(function () {
            Route::get('/create', [DebtController::class, 'create'])->name('create');
            Route::post('/', [DebtController::class, 'store'])->name('store');
            
            // Criar dívida diretamente de uma venda (para integração com sales)
            Route::post('/from-sale', [DebtController::class, 'storeFromSale'])->name('store-from-sale');
        });
        
        // Gerenciar pagamentos - manage_payments permission
        Route::middleware('permissions:manage_payments')->group(function () {
            Route::post('/{debt}/add-payment', [DebtController::class, 'addPayment'])->name('add-payment');
            Route::patch('/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('mark-as-paid');
        });
        
        // Editar dívidas - edit_debts permission
        Route::middleware('permissions:edit_debts')->group(function () {
            Route::get('/{debt}/edit', [DebtController::class, 'edit'])->name('edit');
            Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
            Route::get('/{debt}/edit-data', [DebtController::class, 'editData'])->name('edit-data');
        });
        
        // Cancelar/deletar dívidas - delete_debts permission
        Route::middleware('permissions:delete_debts')->group(function () {
            Route::patch('/{debt}/cancel', [DebtController::class, 'cancel'])->name('cancel');
            Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
        });
        
        // Relatórios - view_reports permission
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/debtors', [DebtController::class, 'debtorsReport'])->name('debtors-report');
            Route::get('/reports/export', [DebtController::class, 'exportDebtorsReport'])->name('export-debtors');
        });
        
        // Utilitários
        Route::get('/search/employees', [DebtController::class, 'searchEmployees'])->name('search-employees');
        Route::get('/search/customers', [DebtController::class, 'searchCustomers'])->name('search-customers');
        Route::post('/update-overdue-status', [DebtController::class, 'updateOverdueStatus'])->name('update-overdue-status');
        Route::post('/{debt}/create-manual-sale', [DebtController::class, 'createManualSale'])->name('create-manual-sale');
    });

    //expense category
    Route::resource('expense-categories', ExpenseCategoryController::class)->only(['store']);
    // ===== DESPESAS =====
    Route::prefix('expenses')->name('expenses.')->group(function () {
        // Visualizar despesas - todos podem
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/details', [ExpenseController::class, 'showData'])->name('details');
        
        // Criar despesas - create_expenses permission
        Route::middleware('permissions:create_expenses')->group(function () {
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
        });
        
        // Editar despesas - edit_expenses permission
        Route::middleware('permissions:edit_expenses')->group(function () {
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        });
        
        // Deletar despesas - delete_expenses permission
        Route::middleware('permissions:delete_expenses')->group(function () {
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ===== MOVIMENTAÇÕES DE ESTOQUE =====
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        // Visualizar movimentações - view_stock_movements permission
        Route::middleware('permissions:view_stock_movements')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
        });
        
        // Criar movimentações - create_stock_movements permission
        Route::middleware('permissions:create_stock_movements')->group(function () {
            Route::get('/create', [StockMovementController::class, 'create'])->name('create');
            Route::post('/', [StockMovementController::class, 'store'])->name('store');
        });
        
        // Gerenciar estoque - manage_stock permission (admin only)
        Route::middleware('permissions:manage_stock')->group(function () {
            Route::get('/{stockMovement}/edit', [StockMovementController::class, 'edit'])->name('edit');
            Route::put('/{stockMovement}', [StockMovementController::class, 'update'])->name('update');
            Route::delete('/{stockMovement}', [StockMovementController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ===== RELATÓRIOS =====
        Route::prefix('reports')->name('reports.')->group(function () {
        // ===== DASHBOARD PRINCIPAL =====
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

        // ===== RELATÓRIOS BÁSICOS =====
        Route::get('/daily-sales', [ReportController::class, 'dailySales'])->name('daily-sales');
        Route::get('/monthly-sales', [ReportController::class, 'monthlySales'])->name('monthly-sales');
        Route::get('/sales-by-product', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');

        // ===== RELATÓRIOS FINANCEIROS =====
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow');

        // ===== ANÁLISES AVANÇADAS =====
        Route::get('/customer-profitability', [ReportController::class, 'customerProfitability'])->name('customer-profitability');
        Route::get('/abc-analysis', [ReportController::class, 'abcAnalysis'])->name('abc-analysis');
        Route::get('/period-comparison', [ReportController::class, 'periodComparison'])->name('period-comparison');
        Route::get('/business-insights', [ReportController::class, 'businessInsights'])->name('business-insights');

        // ===== RELATÓRIOS ESPECIALIZADOS =====
        Route::get('/sales-specialized', [ReportController::class, 'salesReport'])->name('sales-specialized');
        Route::get('/expenses-specialized', [ReportController::class, 'expensesReport'])->name('expenses-specialized');
        Route::get('/comparison-specialized', [ReportController::class, 'comparisonReport'])->name('comparison-specialized');

        // ===== EXPORTAÇÕES =====
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('export.pdf');
        Route::get('/export-csv', [ReportController::class, 'exportCSV'])->name('export.csv');
    });

    
    // ===== USUÁRIOS - manage_users permission =====
    Route::prefix('users')->name('users.')->middleware('permissions:manage_settings')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // Rotas de atividade
        Route::get('/{user}/activity', [UserController::class, 'activity'])->name('activity');
        
        // Rotas de ação
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        
        // Rotas de senhas temporárias
        Route::get('/{user}/temporary-passwords', [UserController::class, 'temporaryPasswords'])->name('temporary-passwords');
        Route::post('/{user}/invalidate-temporary-passwords', [UserController::class, 'invalidateTemporaryPasswords'])->name('invalidate-temporary-passwords');
    });
    
    // ===== API ROUTES =====
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/products/available', [DebtController::class, 'getAvailableProducts'])->name('products.available');
        Route::get('/debts/search-customers', [DebtController::class, 'searchCustomers'])->name('debts.search-customers');
        Route::get('/dashboard/counters', [DashboardController::class, 'getCounters']);
    });
    
    // ===== ADMINISTRAÇÃO - Permissões específicas =====
    Route::middleware('permissions:manage_settings')->group(function () {
        Route::post('/admin/settings', [AdminController::class, 'saveSettings']);
    });
    
    Route::middleware('permissions:backup_system')->group(function () {
        Route::post('/admin/backup', [AdminController::class, 'createBackup']);
    });
    
    Route::middleware('permissions:view_logs')->group(function () {
        Route::get('/admin/logs', [AdminController::class, 'getLogs']);
        Route::get('/admin/logs/export', [AdminController::class, 'exportLogs']);
        Route::delete('/admin/logs/clear', [AdminController::class, 'clearLogs']);
    });
    
    // ===== NOTIFICAÇÕES - Todos os usuários logados =====
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll']);
});

require __DIR__.'/auth.php';