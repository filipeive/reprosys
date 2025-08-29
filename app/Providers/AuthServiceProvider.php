<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ===== ADMIN: Pode fazer TUDO =====
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // ===== Exemplo =====
        Gate::define('view-products', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'user']);
        });

        Gate::define('delete-product', function ($user) {
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
