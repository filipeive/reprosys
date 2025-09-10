<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_phone',
        'subtotal', 'discount_amount', 'discount_percentage', 
        'discount_type', 'discount_reason', 'total_amount', 
        'payment_method', 'notes', 'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sale_date' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Calcular totais da venda com descontos
     */
    public function calculateTotals(): void
    {
        // Subtotal = soma dos (preço original * quantidade) de todos os itens
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->original_unit_price * $item->quantity;
        });

        // Desconto total = soma dos descontos de todos os itens + desconto geral da venda
        $itemsDiscountTotal = $this->items->sum('discount_amount');
        $this->discount_amount = $itemsDiscountTotal;

        // Total final = subtotal - desconto total
        $this->total_amount = $this->subtotal - $this->discount_amount;

        $this->save();
    }

    /**
     * Aplicar desconto geral à venda
     */
    public function applyGeneralDiscount(float $discountValue, string $discountType = 'fixed', string $reason = null): void
    {
        if ($discountType === 'percentage') {
            $discountAmount = ($this->subtotal * $discountValue) / 100;
            $this->discount_percentage = $discountValue;
        } else {
            $discountAmount = $discountValue;
            $this->discount_percentage = ($discountAmount / $this->subtotal) * 100;
        }

        $this->discount_amount += $discountAmount;
        $this->discount_type = $discountType;
        $this->discount_reason = $reason;
        
        $this->calculateTotals();
    }

    /**
     * Obter margem de lucro real considerando descontos
     */
    public function getRealProfit(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->product->purchase_price * $item->quantity;
        });

        return $this->total_amount - $totalCost;
    }

    /**
     * Obter margem de lucro percentual real
     */
    public function getRealProfitMargin(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->product->purchase_price * $item->quantity;
        });

        if ($totalCost == 0) return 0;

        return (($this->total_amount - $totalCost) / $totalCost) * 100;
    }

    /**
     * Verificar se a venda teve desconto
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Obter percentual total de desconto da venda
     */
    public function getTotalDiscountPercentage(): float
    {
        if ($this->subtotal == 0) return 0;
        return ($this->discount_amount / $this->subtotal) * 100;
    }
}
