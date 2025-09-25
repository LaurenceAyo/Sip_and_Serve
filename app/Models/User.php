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

    // Role constants
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_ADMIN = 'admin'; // Support your existing admin role
    const ROLE_MANAGER = 'manager';
    const ROLE_CASHIER = 'cashier';
    const ROLE_KITCHEN = 'kitchen';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->manager_id) {
                $user->manager_id = self::generateUniqueManagerId();
            }

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
        return $this->email === 'laurenceayo7@gmail.com' || $this->role === 'admin' || $this->role === 'administrator';
    }

    public function isAdministrator()
    {
        return $this->isAdmin();
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

    // Fixed hasRole method - supports both single role and array of roles
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            // Handle special case for admin
            if ($roles === 'administrator' || $roles === 'admin') {
                return $this->isAdmin();
            }
            return $this->role === $roles;
        }

        if (is_array($roles)) {
            // Check if user has any of the given roles
            foreach ($roles as $role) {
                if ($role === 'administrator' || $role === 'admin') {
                    if ($this->isAdmin()) {
                        return true;
                    }
                } else {
                    if ($this->role === $role) {
                        return true;
                    }
                }
            }
            return false;
        }

        return false;
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
            case 'dashboard':
            case 'manager':
                return $this->isManager() || $this->isAdmin();
            case 'sales':
                return $this->isManager() || $this->isAdmin();
            case 'product':
                return $this->isManager() || $this->isAdmin();
            case 'cashier':
                return $this->isCashier() || $this->isManager() || $this->isAdmin();
            case 'kitchen':
                return $this->isKitchen() || $this->isManager() || $this->isAdmin();
            default:
                return false;
        }
    }

    // This method is called by CheckManagerAccess middleware
    public function canAccessDashboard()
    {
        return $this->isAdmin() || $this->isManager();
    }

    // Access permission methods for middleware
    public function canAccessAdmin()
    {
        return $this->isAdmin();
    }

    public function canAccessSales()
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canAccessProduct()
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canAccessCashier()
    {
        return $this->isAdmin() || $this->isManager() || $this->isCashier();
    }

    public function canAccessKitchen()
    {
        return $this->isAdmin() || $this->isManager() || $this->isKitchen();
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}