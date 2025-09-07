<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = ['menu_item_id', 'current_stock', 'minimum_stock', 'maximum_stock', 'unit', 'last_restocked_at'];

    protected $casts = [
        'last_restocked_at' => 'datetime'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getStatusAttribute()
    {
        if ($this->current_stock <= ($this->minimum_stock * 0.5)) {
            return 'critical';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            return 'low';
        }
        return 'good';
    }
    // In Inventory model
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'menu_item_id', 'id');
    }
}
