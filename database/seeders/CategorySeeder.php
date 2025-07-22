<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Inventory;
use App\Models\Setting;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed hot coffee drinks',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sweet_Treats',
                'description' => 'Cold and refreshing coffee beverages',
                'sort_order' => 2,
            ],
            [
                'name' => 'LPC_Mix_and_Match',
                'description' => 'Unique and signature beverages',
                'sort_order' => 3,
            ],
            [
                'name' => 'A_la_Carte',
                'description' => 'Fresh baked goods and pastries',
                'sort_order' => 4,
            ],
            [
                'name' => 'Rice_meals',
                'description' => 'Delicious sandwiches and wraps',
                'sort_order' => 5,
            ],
            [
                'name' => 'Combo_meals',
                'description' => 'Sweet treats and desserts',
                'sort_order' => 6,
            ],
            [
                'name' => 'Set_meals',
                'description' => 'Complete meal sets with rice and sides',
                'sort_order' => 7,
            ],
            [
                'name' => 'Noodles_and_pasta',
                'description' => 'Hearty noodle dishes and pasta specialties',
                'sort_order' => 8,
            ],
            [
                'name' => 'Salad',
                'description' => 'Fresh and healthy salad options',
                'sort_order' => 9,
            ],
            [
                'name' => 'Light_meals',
                'description' => 'Quick and satisfying light meal options',
                'sort_order' => 10,
            ],
            [
                'name' => 'Appetizer',
                'description' => 'Tasty starters and finger foods',
                'sort_order' => 11,
            ],
            [
                'name' => 'All_day_breakfast',
                'description' => 'Traditional breakfast favorites served all day',
                'sort_order' => 12,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}