<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $table = 'ingredients';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'cost_per_unit',
        'reorder_level',
        'supplier_info'
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->reorder_level;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->stock_quantity <= 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out-of-stock';
        } elseif ($this->stock_quantity <= $this->reorder_level) {
            return 'low-stock';
        } else {
            return 'in-stock';
        }
    }

    public function getTotalValueAttribute()
    {
        return $this->stock_quantity * $this->cost_per_unit;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Add these relationships
    public function menuItemIngredients()
    {
        return $this->hasMany(MenuItemIngredient::class);
    }
    
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_ingredients')
                    ->withPivot('quantity_needed')
                    ->withoutTimestamps();
    }
}