<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MenuVariant;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost',
        'image',
        'category_id',
        'has_variants',
        'preparation_time',
        'is_available',
        'image_url',
        'stock_quantity'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'has_variants' => 'boolean',
        'is_available' => 'boolean',
        'preparation_time' => 'integer',
        'stock_quantity' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    public function getNameAttribute()
{
    return $this->attributes['name'] ?? $this->attributes['item_name'] ?? 'Unknown Item';
}
}