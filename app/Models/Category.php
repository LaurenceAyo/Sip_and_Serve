<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('assets/' . $this->image);
        }
        return null;
    }
}