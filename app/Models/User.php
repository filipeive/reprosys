<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'manager', 'staff'
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ===== RELACIONAMENTOS =====
    
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'created_by');
    }

    // ===== MÉTODOS DE ROLE =====
    
    /**
     * Verificar se o usuário tem um papel específico
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Verificar se é administrador
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar se é gerente
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Verificar se é funcionário
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // ===== SISTEMA DE PERMISSÕES =====
    
    /**
     * Verificar se o usuário tem uma permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        // Usuários inativos não têm permissões
        if (!$this->is_active) {
            return false;
        }

        // Admins têm todas as permissões
        if ($this->isAdmin()) {
            return true;
        }

        // Verificar permissões do role
        $rolePermissions = $this->getRolePermissions();
        
        return in_array($permission, $rolePermissions);
    }

    /**
     * Verificar múltiplas permissões (usuário deve ter TODAS)
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Verificar múltiplas permissões (usuário deve ter PELO MENOS UMA)
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Obter permissões do role atual
     */
    public function getRolePermissions(): array
    {
        $permissions = config('auth_permissions.role_permissions', []);
        
        return $permissions[$this->role] ?? [];
    }

    /**
     * Verificar se pode editar um recurso (próprio ou com permissão admin)
     */
    public function canEdit($resource): bool
    {
        // Admins podem editar tudo
        if ($this->isAdmin()) {
            return true;
        }

        // Managers podem editar recursos da empresa
        if ($this->isManager()) {
            return true;
        }

        // Staff pode editar apenas próprios recursos
        if (method_exists($resource, 'belongsToUser')) {
            return $resource->belongsToUser($this);
        }

        // Fallback: verificar user_id ou created_by
        return $resource->user_id === $this->id || 
               (isset($resource->created_by) && $resource->created_by === $this->id);
    }

    /**
     * Verificar se pode visualizar um recurso
     */
    public function canView($resource): bool
    {
        // Admins e managers podem ver tudo
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        // Staff pode ver próprios recursos
        return $this->canEdit($resource);
    }

    /**
     * Verificar se pode deletar um recurso
     */
    public function canDelete($resource): bool
    {
        // Apenas admins podem deletar por padrão
        if ($this->isAdmin()) {
            return true;
        }

        // Managers podem deletar alguns recursos
        if ($this->isManager() && $this->hasPermission('delete_' . class_basename($resource))) {
            return true;
        }

        return false;
    }

    // ===== SCOPES =====
    
    /**
     * Scope para usuários ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para usuários por role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope para admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope para funcionários
     */
    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    // ===== ACCESSORS =====
    
    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        $roles = [
            'admin' => 'Administrador',
            'manager' => 'Gerente',
            'staff' => 'Funcionário',
        ];

        return $roles[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= substr($name, 0, 1);
        }
        
        return strtoupper($initials);
    }

    /**
     * Get first name
     */
    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0];
    }

    // ===== MÉTODOS DE ESTATÍSTICAS =====
    
    /**
     * Obter estatísticas do usuário
     */
    public function getStats(): array
    {
        if (!$this->is_active) {
            return [
                'sales_count' => 0,
                'sales_total' => 0,
                'orders_count' => 0,
                'expenses_count' => 0,
                'products_created' => 0,
            ];
        }

        return [
            'sales_count' => $this->sales()->count(),
            'sales_total' => $this->sales()->sum('total_amount'),
            'orders_count' => $this->orders()->count(),
            'expenses_count' => $this->expenses()->count(),
            'products_created' => $this->products()->count(),
        ];
    }

    /**
     * Obter vendas do mês atual
     */
    public function getCurrentMonthSales()
    {
        return $this->sales()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Obter performance mensal
     */
    public function getMonthlyPerformance(): array
    {
        $currentMonth = $this->getCurrentMonthSales();
        
        return [
            'sales_count' => $currentMonth->count(),
            'sales_total' => $currentMonth->sum('total_amount'),
            'average_sale' => $currentMonth->count() > 0 ? $currentMonth->avg('total_amount') : 0,
        ];
    }

    // ===== MÉTODOS DE ATIVAÇÃO =====
    
    /**
     * Ativar usuário
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Desativar usuário
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Alternar status ativo/inativo
     */
    public function toggleStatus(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read', false)->latest();
    }
}