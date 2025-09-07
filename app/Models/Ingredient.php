<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $table = 'ingredients';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'cost_per_unit',
        'reorder_level',
        'supplier_info'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor to check if ingredient is low in stock
     */
    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->reorder_level;
    }

    /**
     * Accessor to check if ingredient is out of stock
     */
    public function getIsOutOfStockAttribute()
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Accessor to get stock status
     */
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

    /**
     * Accessor to get total value of current stock
     */
    public function getTotalValueAttribute()
    {
        return $this->stock_quantity * $this->cost_per_unit;
    }

    /**
     * Scope to get low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    /**
     * Scope to get out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope to get in stock items
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * If you have relationships with other models, add them here
     * For example, if ingredients are used in menu items:
     */
    
    // public function menuItemIngredients()
    // {
    //     return $this->hasMany(MenuItemIngredient::class);
    // }
    
    // public function menuItems()
    // {
    //     return $this->belongsToMany(MenuItem::class, 'menu_item_ingredients')
    //                 ->withPivot('quantity_needed', 'unit');
    // }
}