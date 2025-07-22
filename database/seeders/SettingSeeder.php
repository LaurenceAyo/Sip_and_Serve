<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Inventory;


class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'cafe_name', 'value' => "L'Primero Café"],
            ['key' => 'cafe_address', 'value' => 'Legaspi, Bicol Region, Philippines'],
            ['key' => 'cafe_phone', 'value' => '+63 XX XXXX XXXX'],
            ['key' => 'tax_rate', 'value' => '0.12'],
            ['key' => 'currency', 'value' => 'PHP'],
            ['key' => 'receipt_footer', 'value' => 'Thank you for visiting L\'Primero Café!'],
            ['key' => 'order_timeout', 'value' => '30'], // minutes
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}