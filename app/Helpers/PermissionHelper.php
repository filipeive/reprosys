<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionHelper
{
    /**
     * Verifica se o usuário tem a role específica
     */
    public static function hasRole(string $roleName): bool
    {
        if (!Auth::check()) return false;

        $userRole = DB::table('roles')
            ->where('id', Auth::user()->role_id)
            ->value('name');

        return strtolower($userRole) === strtolower($roleName);
    }

    /**
     * Checar se é admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    /**
     * Checar se é manager
     */
    public static function isManager(): bool
    {
        return self::hasRole('manager');
    }

    /**
     * Checar se é staff
     */
    public static function isStaff(): bool
    {
        return self::hasRole('staff');
    }

    /**
     * Retorna o nível/role do usuário
     */
    public static function getUserLevel(): ?string
    {
        if (!Auth::check()) return 'guest';

        return DB::table('roles')
            ->where('id', Auth::user()->role_id)
            ->value('name');
    }

    /**
     * Verificar permissões customizadas (opcional)
     */
    public static function userCan(string $permission): bool
    {
        $userLevel = strtolower(self::getUserLevel());

        if ($userLevel === 'admin') {
            return true; // Admin tem todas permissões
        }

        // Aqui você pode carregar permissões da tabela ou config
        $rolePermissions = config('auth_permissions.role_permissions', []);
        return isset($rolePermissions[$userLevel]) && in_array($permission, $rolePermissions[$userLevel]);
    }
}
