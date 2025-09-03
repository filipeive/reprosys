<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\PermissionService;

class User extends Authenticatable
{
    use HasFactory;
    
    // Remova o trait Notifiable para usar um relacionamento personalizado
    // use Notifiable; // <<-- COMENTE OU REMOVA ESTA LINHA

    // ===== PROPRIEDADES =====

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
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

    // ===== RELACIONAMENTOS (CORREÇÃO) =====
    
    // Relacionamento com o modelo Role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Define a relação com a sua tabela de notificações personalizada.
     * Esta é a CORREÇÃO para o seu problema.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    /**
     * Retorna apenas as notificações não lidas.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('read', false);
    }

    // ===== ACCESSOR (CORREÇÃO) =====

    /**
     * Obtém o nome da role através do relacionamento.
     */
    public function getRoleAttribute()
    {
        return $this->role()->first()->name ?? null;
    }

    // ===== MÉTODOS DE PAPEL (ROLES) E PERMISSÕES =====

    protected function permissions(): PermissionService
    {
        return new PermissionService($this);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->hasPermission($permission);
    }
    
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    // ===== AUTORIZAÇÃO DE RECURSOS =====

    public function canEdit($resource): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return $resource->user_id === $this->id ||
               (property_exists($resource, 'created_by') && $resource->created_by === $this->id);
    }

    public function canView($resource): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return $this->canEdit($resource);
    }

    public function canDelete($resource): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isManager()) {
            return $this->hasPermission('delete_' . class_basename($resource));
        }

        return false;
    }

    // ===== SCOPES DE CONSULTA =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('role', function($q) use ($role) {
            $q->where('name', $role);
        });
    }

    public function scopeAdmins($query)
    {
        return $query->byRole('admin');
    }

    public function scopeStaff($query)
    {
        return $query->byRole('staff');
    }

    // ===== ACCESSORS SECUNDÁRIOS =====

    public function getRoleDisplayAttribute(): string
    {
        $roles = [
            'admin' => 'Administrador',
            'manager' => 'Gerente',
            'staff' => 'Funcionário',
        ];

        return $roles[$this->role] ?? ucfirst($this->role);
    }

    public function getStatusDisplayAttribute(): string
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= substr($name, 0, 1);
        }
        return strtoupper($initials);
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0] ?? '';
    }
}