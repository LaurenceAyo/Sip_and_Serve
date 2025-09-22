<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'created_by',
        'manager_id',
        'role',
        'status',
        'permissions',
        'password_reset_required',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'permissions' => 'array',
            'password_reset_required' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->manager_id) {
                $user->manager_id = self::generateUniqueManagerId();
            }

            // Set default values for admin fields if not provided
            if (!$user->role) {
                $user->role = 'cashier';
            }

            if (!$user->status) {
                $user->status = 'active';
            }
        });
    }

    public static function generateUniqueManagerId()
    {
        do {
            $managerId = 'MNG' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('manager_id', $managerId)->exists());

        return $managerId;
    }

    // Enhanced role checking methods
    public function isAdmin()
    {
        return $this->email === 'laurenceayo7@gmail.com' || $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    public function isKitchen()
    {
        return $this->role === 'kitchen';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
    
    public function canAccess($area)
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        switch ($area) {
            case 'admin':
                return $this->isAdmin();
            case 'manager':
                return $this->isManager() || $this->isAdmin();
            case 'cashier':
                return $this->isCashier() || $this->isAdmin();
            case 'kitchen':
                return $this->isKitchen() || $this->isAdmin();
            default:
                return false;
        }
    }
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
