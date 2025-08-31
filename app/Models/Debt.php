<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'sale_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'customer_document',
        'original_amount',
        'paid_amount',
        'remaining_amount',
        'debt_date',
        'due_date',
        'status',
        'description',
        'notes'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
                    ->where('status', '!=', 'paid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-warning text-dark',
            'partial' => 'bg-info',
            'paid' => 'bg-success',
            'overdue' => 'bg-danger',
            'cancelled' => 'bg-secondary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'active' => 'Ativa',
            'partial' => 'Parcial',
            'paid' => 'Paga',
            'overdue' => 'Vencida',
            'cancelled' => 'Cancelada'
        ];

        return $texts[$this->status] ?? 'Status Desconhecido';
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->due_date || $this->status === 'paid') {
            return 0;
        }

        return max(0, now()->diffInDays($this->due_date, false));
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && 
               $this->due_date < now()->toDateString() && 
               $this->status !== 'paid';
    }

    // Métodos de negócio
    public function addPayment(float $amount, string $paymentMethod = 'cash', string $notes = null): DebtPayment
    {
        $payment = $this->payments()->create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => now()->toDateString(),
            'notes' => $notes
        ]);

        $this->updatePaymentStatus();

        return $payment;
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->remaining_amount = $this->original_amount - $totalPaid;

        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
            $this->remaining_amount = 0;
        } elseif ($totalPaid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = $this->is_overdue ? 'overdue' : 'active';
        }

        $this->save();
    }

    public function getTotalPaidAmount(): float
    {
        return $this->payments()->sum('amount');
    }

    public function canAddPayment(): bool
    {
        return $this->status !== 'paid' && $this->status !== 'cancelled';
    }

    public function markAsPaid(): void
    {
        $remainingAmount = $this->remaining_amount;
        
        if ($remainingAmount > 0) {
            $this->addPayment($remainingAmount, 'cash', 'Marcado como pago automaticamente');
        }
    }

    // Método para atualizar status baseado na data
    public function updateOverdueStatus(): void
    {
        if ($this->is_overdue && $this->status === 'active') {
            $this->status = 'overdue';
            $this->save();
        }
    }
    public function items(): HasMany
    {
        return $this->hasMany(DebtItem::class);
    }

    // Método para recalcular total baseado nos itens
    public function recalculateTotal(): void
    {
        $total = $this->items->sum('total_price');
        $this->original_amount = $total;
        
        // Recalcular remaining_amount mantendo a proporção dos pagamentos
        $paidAmount = $this->payments->sum('amount');
        $this->remaining_amount = max(0, $total - $paidAmount);
        
        $this->save();
        $this->updatePaymentStatus();
    }
}