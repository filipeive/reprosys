<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('delete-product', function ($user) {
            return $user->is_admin; // Supondo que você tenha um campo 'is_admin' no modelo User
        });
    }
}