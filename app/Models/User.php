<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'manager_id',
        'role',                    // Added for admin system
        'status',                  // Added for admin system
        'permissions',             // Added for admin system
        'last_login_at',           // Added for admin system
        'created_by',              // Added for admin system
        'password_reset_required', // Added for admin system
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',           // Added for admin system
            'permissions' => 'array',                // Added for admin system
            'password_reset_required' => 'boolean',  // Added for admin system
        ];
    }

    /**
     * Boot method to auto-generate manager_id for new users
     */
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

    /**
     * Generate a unique manager ID
     */
    public static function generateUniqueManagerId()
    {
        do {
            $managerId = 'MNG' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('manager_id', $managerId)->exists());
        
        return $managerId;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->email === 'laurenceayo7@gmail.com' || $this->role === 'admin';
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $userPermissions = $this->permissions ?? [];
        return in_array($permission, $userPermissions) || in_array('all', $userPermissions);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}