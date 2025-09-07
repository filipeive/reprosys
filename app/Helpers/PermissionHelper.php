<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;

class PermissionHelper
{
    /**
     * Obtém o usuário autenticado, se existir e estiver ativo.
     * Retorna null se não estiver autenticado ou inativo.
     */
    protected static function getAuthenticatedUser()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        // Verifica se a conta está ativa
        if (property_exists($user, 'is_active') && !$user->is_active) {
            return null;
        }

        return $user;
    }

    /**
     * Verifica se o usuário tem a role específica.
     */
    public static function hasRole(string $roleName): bool
    {
        $user = self::getAuthenticatedUser();
        return $user ? strtolower($user->role) === strtolower($roleName) : false;
    }

    /**
     * Checa se o usuário é um administrador.
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    /**
     * Checa se o usuário é um gerente.
     */
    public static function isManager(): bool
    {
        return self::hasRole('manager');
    }

    /**
     * Checa se o usuário é um membro da equipe.
     */
    public static function isStaff(): bool
    {
        return self::hasRole('staff');
    }

    /**
     * Retorna o nível ou role do usuário.
     */
    public static function getUserLevel(): string
    {
        $user = self::getAuthenticatedUser();
        return $user ? $user->role : 'guest';
    }

    /**
     * Verifica se o usuário tem uma permissão específica.
     */
    public static function userCan(string $permission): bool
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return false;
        }

        // Admins têm todas as permissões
        if (self::isAdmin()) {
            return true;
        }

        // Usar o método do modelo User se existir
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        // Fallback para verificação manual via arquivo de configuração
        return self::checkPermissionFallback($user->role, $permission);
    }

    /**
     * Verifica se o usuário tem todas as permissões listadas.
     */
    public static function userCanAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::userCan($permission)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Verifica se o usuário tem pelo menos uma das permissões.
     */
    /* public static function userCanAny(array $permissions): bool
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return false;
        }

        $service = new PermissionService($user);
        return $service->hasAnyPermission($permissions);
    } */
    /**
     * Verifica se o usuário tem pelo menos uma das permissões listadas.
     */
    public static function userCanAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::userCan($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obter todas as permissões do usuário atual.
     */
    public static function getAllPermissions(): array
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return [];
        }

        if (self::isAdmin()) {
            return array_keys(config('auth_permissions.all_permissions', []));
        }

        if (method_exists($user, 'getRolePermissions')) {
            return $user->getRolePermissions();
        }

        $rolePermissions = config('auth_permissions.role_permissions', []);
        return $rolePermissions[$user->role] ?? [];
    }

    /**
     * Verifica se o usuário pode editar um recurso.
     */
    public static function canEdit($resource): bool
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return false;
        }

        if (self::isAdmin() || self::isManager()) {
            return true;
        }

        return isset($resource->user_id) && $resource->user_id === $user->id;
    }

    /**
     * Verifica se o usuário pode visualizar um recurso.
     */
    public static function canView($resource): bool
    {
        if (self::isAdmin() || self::isManager()) {
            return true;
        }

        return self::canEdit($resource);
    }

    /**
     * Verifica se o usuário pode deletar um recurso.
     */
    public static function canDelete($resource): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        if (self::isManager()) {
            $className = strtolower(class_basename($resource));
            return self::userCan('delete_' . $className);
        }

        return false;
    }

    /**
     * Obtém a exibição do role atual para o usuário.
     */
    public static function getRoleDisplay(): string
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return 'Visitante';
        }

        $roles = [
            'admin' => 'Administrador',
            'manager' => 'Gerente',
            'staff' => 'Funcionário',
        ];

        return $roles[$user->role] ?? ucfirst($user->role);
    }

    /**
     * Verifica se um item de menu deve ser exibido.
     */
    public static function shouldShowMenuItem(string $menuItem): bool
    {
        $menuConfig = config('auth_permissions.menu_items.' . $menuItem, []);

        if (self::isAdmin()) {
            return true;
        }

        if (empty($menuConfig)) {
            return self::getAuthenticatedUser() !== null;
        }

        if (isset($menuConfig['permission'])) {
            return self::userCan($menuConfig['permission']);
        }

        return false;
    }

    /**
     * Obtém informações do usuário atual.
     */
    public static function getCurrentUserInfo(): array
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return [
                'name' => 'Visitante',
                'role' => 'guest',
                'role_display' => 'Visitante',
            ];
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'role_display' => self::getRoleDisplay(),
            'initials' => method_exists($user, 'getInitialsAttribute') ? $user->initials : strtoupper(substr($user->name, 0, 2)),
        ];
    }
     public static function getProductPermissions()
        {
            return [
                'view_products' => 'Visualizar produtos e serviços',
                'create_products' => 'Criar novos produtos e serviços',
                'edit_products' => 'Editar produtos e serviços',
                'delete_products' => 'Excluir produtos e serviços',
                'adjust_stock' => 'Ajustar estoque de produtos',
                'view_categories' => 'Visualizar categorias',
                'create_categories' => 'Criar novas categorias',
                'edit_categories' => 'Editar categorias',
                'delete_categories' => 'Excluir categorias',
            ];
        }
}