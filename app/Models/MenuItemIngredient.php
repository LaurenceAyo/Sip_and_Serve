<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    use HasFactory;

    protected $table = 'menu_item_ingredients';

    protected $fillable = [
        'menu_item_id',
        'ingredient_id',
        'quantity_needed'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}