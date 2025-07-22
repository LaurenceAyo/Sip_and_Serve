<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuItemSeeder;
use Database\Seeders\SettingSeeder;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Inventory;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            SettingSeeder::class,
        ]);
    }
}