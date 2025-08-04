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
        //delete
        Gate::define('delete-category', function ($user) {
            return $user->role === 'admin';
        });
        //delet e stock movement
        Gate::define('delete-stock-movement', function ($user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });
        Gate::define('delete-product', function ($user) {
           return $user->role === 'admin';  
        });
        Gate::define('create-sales', function ($user) {
            return $user->role === 'admin' || $user->role === 'sales';
        });
        Gate::define('view-reports', function ($user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });
        //edit users
        Gate::define('edit-users', function ($user) {
            return $user->role === 'admin';
        });
        //all previleages
        Gate::define('admin', function($user){
            return $user->role === 'admin';
        });
    }

}
