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
        'manager_id',  // Added this
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
        return $this->email === 'admin@example.com' || $this->name === 'Alice Admin';
    }
}