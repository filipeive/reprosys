<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
    ];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

   public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

}
