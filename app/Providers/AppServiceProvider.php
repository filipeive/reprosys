<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\PermissionHelper;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!function_exists('userCan')) {
            function userCan($permission) {
                return PermissionHelper::userCan($permission);
            }
        }

        if (!function_exists('isAdmin')) {
            function isAdmin() {
                return PermissionHelper::isAdmin();
            }
        }

        if (!function_exists('isManager')) {
            function isManager() {
                return PermissionHelper::isManager();
            }
        }

        if (!function_exists('isStaff')) {
            function isStaff() {
                return PermissionHelper::isStaff();
            }
        }

        if (!function_exists('getUserLevel')) {
            function getUserLevel() {
                return PermissionHelper::getUserLevel();
            }
        }
    }
}
