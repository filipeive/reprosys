<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_phone',
        'total_amount', 'payment_method', 'notes', 'sale_date'
    ];

    protected $casts = [
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

    public function calculateTotal(): void
    {
        $this->total_amount = $this->items->sum('total_price');
        $this->save();
    }
}