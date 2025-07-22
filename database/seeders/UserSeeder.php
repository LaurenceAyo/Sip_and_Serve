<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users first (optional)
        // User::truncate();

        // Method 1: Use updateOrCreate to avoid duplicates
        User::updateOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name' => 'Alice Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'laurenceayo7.com'],
            [
                'name' => 'Laurence Ayo',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Method 3: Use factory for random users (won't duplicate)
        User::factory(10)->create();
    }
}