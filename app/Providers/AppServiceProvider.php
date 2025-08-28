<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ===== ADMIN: Pode fazer TUDO =====
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });
        //authorize update
        Gate::define('update-expense', function ($user, $expense) {
            return in_array($user->role, ['admin', 'manager']) || ($user->role === 'sales' && $expense->user_id === $user->id);
        });
        // ===== CATEGORIAS =====
        Gate::define('view-categories', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'user']);
        });

        Gate::define('create-category', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('edit-category', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('delete-category', function ($user) {
            return $user->role === 'admin';
        });

        // ===== PRODUTOS =====
        Gate::define('view-products', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'user']);
        });

        Gate::define('create-product', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('edit-product', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('delete-product', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('adjust-stock', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'sales']);
        });

        // ===== DESPESAS =====
        Gate::define('view-expenses', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'user']);
        });

        Gate::define('create-expense', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'sales']);
        });

        Gate::define('edit-expense', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('delete-expense', function ($user) {
            return $user->role === 'admin';
        });

        // ===== VENDAS =====
        Gate::define('create-sales', function ($user) {
            return in_array($user->role, ['admin', 'sales']);
        });

        Gate::define('view-sales', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'sales']);
        });

        // ===== RELATÓRIOS =====
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('export-reports', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });
        Gate::define('export-excel', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // ===== USUÁRIOS =====
        Gate::define('view-users', function ($user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('edit-users', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('delete-users', function ($user) {
            return $user->role === 'admin';
        });

        // ===== MOVIMENTAÇÕES DE ESTOQUE =====
        Gate::define('delete-stock-movement', function ($user) {
            return $user->role === 'admin';
        });

        // ===== GATE GLOBAL: ADMIN PODE TUDO =====
        Gate::before(function ($user, $ability) {
            if ($user->role === 'admin') {
                return true; // Admin pode tudo
            }
        });
    }

}
