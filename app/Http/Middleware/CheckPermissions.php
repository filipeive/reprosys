<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Verificar se o usuário está logado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar se a conta está ativa (se o campo existir)
        if (property_exists($user, 'is_active') && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['account' => 'Sua conta está inativa. Entre em contato com o administrador.']);
        }

        // Se não há permissões específicas, apenas verificar se está logado e ativo
        if (empty($permissions)) {
            return $next($request);
        }

        // Verificar permissões específicas
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($user, $permission)) {
                // Para requisições AJAX, retornar JSON
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para executar esta ação.'
                    ], 403);
                }

                // Para requisições normais, redirect com erro
                return redirect()->back()
                    ->withErrors(['permission' => 'Você não tem permissão para acessar esta funcionalidade.'])
                    ->withInput();
            }
        }

        return $next($request);
    }

    /**
     * Verificar se o usuário tem a permissão especificada
     */
    private function hasPermission($user, $permission): bool
    {
        // Verificar se o método isAdmin existe
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        // Fallback: verificar se role é admin
        if (property_exists($user, 'role') && $user->role === 'admin') {
            return true;
        }

        // Definir permissões por role
        $rolePermissions = $this->getPermissionsByRole($user->role ?? 'staff');
        
        return in_array($permission, $rolePermissions);
    }

    /**
     * Obter permissões por role
     */
    private function getPermissionsByRole($role): array
    {
        $permissions = [
            'admin' => [
                'view_dashboard', 'view_reports', 'export_reports',
                'manage_users', 'create_users', 'edit_users', 'delete_users', 'activate_users',
                'manage_products', 'create_products', 'edit_products', 'delete_products', 'adjust_stock',
                'manage_categories', 'create_categories', 'edit_categories', 'delete_categories',
                'view_sales', 'create_sales', 'edit_sales', 'delete_sales',
                'manage_orders', 'create_orders', 'edit_orders', 'delete_orders', 'convert_orders',
                'manage_debts', 'create_debts', 'edit_debts', 'delete_debts', 'manage_payments',
                'manage_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses',
                'manage_stock', 'view_stock_movements', 'create_stock_movements',
                'manage_settings', 'backup_system', 'view_logs',
            ],
            
            'manager' => [
                'view_dashboard', 'view_reports', 'export_reports',
                'view_products', 'create_products', 'edit_products', 'adjust_stock', 'view_categories',
                'view_sales', 'create_sales', 'edit_sales',
                'view_orders', 'create_orders', 'edit_orders', 'convert_orders',
                'view_debts', 'create_debts', 'edit_debts', 'manage_payments',
                'view_expenses', 'create_expenses', 'edit_expenses',
                'manage_stock', 'view_stock_movements', 'create_stock_movements',
            ],
            
            'staff' => [
                'view_dashboard',
                'view_products', 'edit_products', 'adjust_stock',
                'view_sales', 'create_sales', 'edit_own_sales',
                'view_orders', 'create_orders', 'edit_own_orders',
                'view_debts', 'create_debts', 'manage_payments',
                'view_expenses', 'create_expenses',
                'view_stock_movements', 'view_basic_reports',
            ]
        ];

        return $permissions[$role] ?? [];
    }

    /**
     * Método estático para verificar permissões em views
     */
    public static function userCan($permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $middleware = new self();
        return $middleware->hasPermission(Auth::user(), $permission);
    }
}