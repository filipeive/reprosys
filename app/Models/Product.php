<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'category_id', 'name', 'description', 'type', 
        'purchase_price', 'selling_price', 'stock_quantity',
        'min_stock_level', 'unit', 'is_active',
        'deleted_at','original_name',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function updateStock(int $quantity, string $type = 'out'): void
    {
        if ($type === 'out') {
            $this->decrement('stock_quantity', $quantity);
        } else {
            $this->increment('stock_quantity', $quantity);
        }
    }
    // Accessor para exibir o nome com marcação de exclusão
    public function getNameAttribute($value)
    {
        if ($this->is_deleted) {
            return $value . ' 🚫 (EXCLUÍDO)';
        }
        return $value;
    }
       // Mutator para salvar o nome original ao excluir
    public function markAsDeleted()
    {
        if (!$this->is_deleted) {
            $this->original_name = $this->name;
            $this->name = $this->name . ' (EXCLUÍDO)';
            $this->is_deleted = true;
            $this->deleted_at = now();
            $this->is_active = false;
            $this->save();
        }
    }
}
