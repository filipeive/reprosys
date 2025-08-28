<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'description',
        'estimated_amount',
        'advance_payment',
        'delivery_date',
        'priority',
        'status',
        'payment_status',
        'notes',
        'internal_notes'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function debt(): HasOne
    {
        return $this->hasOne(Debt::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('delivery_date', '<', now())
                    ->whereNotIn('status', ['delivered', 'cancelled']);
    }

    // Accessors
    public function getRemainingAmountAttribute()
    {
        return $this->estimated_amount - $this->advance_payment;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning text-dark',
            'in_progress' => 'bg-primary',
            'completed' => 'bg-success',
            'delivered' => 'bg-info',
            'cancelled' => 'bg-danger'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Pendente',
            'in_progress' => 'Em Andamento',
            'completed' => 'Concluído',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];

        return $texts[$this->status] ?? 'Status Desconhecido';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'bg-secondary',
            'medium' => 'bg-info',
            'high' => 'bg-warning text-dark',
            'urgent' => 'bg-danger'
        ];

        return $badges[$this->priority] ?? 'bg-secondary';
    }

    public function getPriorityTextAttribute()
    {
        $texts = [
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'urgent' => 'Urgente'
        ];

        return $texts[$this->priority] ?? 'Média';
    }

    // Métodos de negócio
    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeDelivered(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }

    public function isOverdue(): bool
    {
        return $this->delivery_date && 
               $this->delivery_date < now() && 
               !in_array($this->status, ['delivered', 'cancelled']);
    }

    // Método para converter pedido em venda
    public function convertToSale(): Sale
    {
        $sale = Sale::create([
            'user_id' => $this->user_id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'total_amount' => $this->estimated_amount,
            'payment_method' => 'cash', // padrão
            'notes' => "Convertido do pedido #{$this->id} - {$this->description}",
            'sale_date' => now()->toDateString()
        ]);

        // Criar itens da venda baseado nos itens do pedido
        foreach ($this->items as $orderItem) {
            $sale->items()->create([
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->unit_price,
                'total_price' => $orderItem->total_price
            ]);
        }

        // Atualizar status do pedido
        $this->update(['status' => 'delivered']);

        return $sale;
    }
}