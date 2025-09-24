<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => Role::ADMINISTRATOR,
                'display_name' => 'Administrator',
                'description' => 'Full system access - can access all areas and manage users'
            ],
            [
                'name' => Role::MANAGER,
                'display_name' => 'Manager',
                'description' => 'Can access dashboard, sales, products, and cashier functions'
            ],
            [
                'name' => Role::CASHIER,
                'display_name' => 'Cashier',
                'description' => 'Can access cashier interface and process orders'
            ],
            [
                'name' => Role::KITCHEN_STAFF,
                'display_name' => 'Kitchen Staff',
                'description' => 'Can access kitchen interface to manage orders'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}