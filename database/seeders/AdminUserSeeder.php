<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'laurenceayo7@gmail.com'],
            [
                'name' => 'Laurence Ayo Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'permissions' => ['all']
            ]
        );
    }
}