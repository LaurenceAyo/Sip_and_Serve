<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    // Define role constants
    const ADMINISTRATOR = 'administrator';
    const MANAGER = 'manager';
    const CASHIER = 'cashier';
    const KITCHEN_STAFF = 'kitchen_staff';

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper methods for role checking
    public static function getAdministratorRole()
    {
        return self::where('name', self::ADMINISTRATOR)->first();
    }

    public static function getManagerRole()
    {
        return self::where('name', self::MANAGER)->first();
    }

    public static function getCashierRole()
    {
        return self::where('name', self::CASHIER)->first();
    }

    public static function getKitchenStaffRole()
    {
        return self::where('name', self::KITCHEN_STAFF)->first();
    }
}