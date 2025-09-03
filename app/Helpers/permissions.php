<?php

use App\Helpers\PermissionHelper;

if (!function_exists('userCan')) {
    function userCan(string $permission): bool
    {
        return PermissionHelper::userCan($permission);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return PermissionHelper::isAdmin();
    }
}

if (!function_exists('isManager')) {
    function isManager(): bool
    {
        return PermissionHelper::isManager();
    }
}

if (!function_exists('isStaff')) {
    function isStaff(): bool
    {
        return PermissionHelper::isStaff();
    }
}

if (!function_exists('getUserLevel')) {
    function getUserLevel(): string
    {
        return PermissionHelper::getUserLevel();
    }
}
