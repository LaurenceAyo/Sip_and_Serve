<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'price', 'cost', 
        'description', 'is_active', 'stock_quantity'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function variants()
    {
        return $this->hasMany(MenuItemVariant::class);
    }
}