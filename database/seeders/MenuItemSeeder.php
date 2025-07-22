<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing categories from CategorySeeder
        $coffeeCategory = Category::where('name', 'Coffee')->first();
        $sweetTreatsCategory = Category::where('name', 'Sweet_Treats')->first();
        $lpcMixCategory = Category::where('name', 'LPC_Mix_and_Match')->first();
        $alaCarteCategory = Category::where('name', 'A_la_Carte')->first();
        $riceMealsCategory = Category::where('name', 'Rice_meals')->first();
        $comboMealsCategory = Category::where('name', 'Combo_meals')->first();
        $setMealsCategory = Category::where('name', 'Set_meals')->first();
        $noodlesPastaCategory = Category::where('name', 'Noodles_and_pasta')->first();
        $saladCategory = Category::where('name', 'Salad')->first();
        $lightMealsCategory = Category::where('name', 'Light_meals')->first();
        $appetizerCategory = Category::where('name', 'Appetizer')->first();
        $allDayBreakfastCategory = Category::where('name', 'All_day_breakfast')->first();

        // COFFEE ITEMS (Hot & Iced)
        $this->createMenuItem($coffeeCategory, 'Americano', 'Rich espresso with hot water', 95.00, 35.00, 'americano.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Latte', 'Espresso with steamed milk and beautiful latte art', 120.00, 45.00, 'latte.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Cappuccino', 'Perfect balance of espresso, steamed milk, and foam', 110.00, 42.00, 'cappuccino.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Mocha Latte', 'Rich chocolate and espresso with steamed milk', 135.00, 50.00, 'mocha_latte.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Caramel Macchiato', 'Espresso with vanilla syrup, steamed milk, and caramel drizzle', 140.00, 52.00, 'caramel_macchiato.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Cortado', 'Equal parts espresso and warm milk', 105.00, 38.00, 'cortado.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Espresso', 'Pure, concentrated coffee shot', 75.00, 25.00, 'espresso.jpg', false);
        $this->createMenuItem($coffeeCategory, 'Spanish Latte', 'Espresso with condensed milk and steamed milk', 125.00, 48.00, 'spanish_latte.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Piccolo Latte', 'Small but mighty espresso with steamed milk', 85.00, 32.00, 'piccolo_latte.jpg', false);
        $this->createMenuItem($coffeeCategory, 'Magic Coffee', 'Double ristretto with steamed milk', 100.00, 36.00, 'magic_coffee.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Bulletproof Coffee', 'Coffee blended with grass-fed butter and MCT oil', 150.00, 55.00, 'bulletproof_coffee.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Iced Americano', 'Chilled espresso with cold water over ice', 105.00, 40.00, 'iced_americano.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Cold Brew', 'Smooth, slow-brewed coffee served cold', 115.00, 43.00, 'cold_brew_blanc.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Cold Brew Negro', 'Black cold brew coffee, bold and refreshing', 110.00, 41.00, 'cold_brew_negro.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Iced Latte', 'Espresso with cold milk over ice', 125.00, 47.00, 'iced_latte.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Dirty Matcha Latte', 'Matcha latte with a shot of espresso', 145.00, 54.00, 'dirty_matcha_latte.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Doppio', 'Double shot of espresso over ice', 85.00, 30.00, 'doppio.jpg', false);
        $this->createMenuItem($coffeeCategory, 'Pour Over Coffee', 'Hand-poured coffee with precision brewing', 120.00, 45.00, 'pour_over_coffee.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Long Black', 'Hot water with double shot espresso', 95.00, 35.00, 'long_black.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Black Magic Coffee', 'Dark roast coffee with magical flavor', 105.00, 40.00, 'black_magic_coffee.jpg', true);
        $this->createMenuItem($coffeeCategory, 'Vanilla Latte', 'Smooth vanilla-flavored latte', 130.00, 48.00, 'vanilla_latte.jpg', true);

        // SWEET TREATS (Pastries & Desserts)
        $this->createMenuItem($sweetTreatsCategory, 'Butter Croissant', 'Flaky, buttery French pastry', 85.00, 30.00, 'butter_croissant.jpg', false);
        $this->createMenuItem($sweetTreatsCategory, 'Blueberry Muffin', 'Fresh baked muffin with blueberries', 75.00, 25.00, 'blueberry_muffin.jpg', false);
        $this->createMenuItem($sweetTreatsCategory, 'Buttermilk Pancakes', 'Fluffy pancakes served with syrup and butter', 165.00, 60.00, 'buttermilk_pancakes.jpg', false);
        $this->createMenuItem($sweetTreatsCategory, 'Waffle with Vanilla Ice Cream', 'Crispy waffle topped with vanilla ice cream', 185.00, 65.00, 'waffle_with_vanilla_ice_cream.jpg', false);
        $this->createMenuItem($sweetTreatsCategory, 'Brownies with Vanilla Ice Cream', 'Rich chocolate brownies with vanilla ice cream', 145.00, 52.00, 'brownies_with_vanilla_ice_cream.jpg', false);
        $this->createMenuItem($sweetTreatsCategory, 'Affogato', 'Vanilla ice cream "drowned" in espresso', 115.00, 42.00, 'affogato.jpg', false);

        // A LA CARTE
        $this->createMenuItem($alaCarteCategory, 'Fish and Chips', 'Beer-battered fish with crispy fries', 285.00, 120.00, 'fish_and_chips.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Chicken Chop with Black Pepper Sauce', 'Grilled chicken with black pepper sauce', 265.00, 110.00, 'chicken_chop_with_black_pepper_sauce.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Chicken Cutlet', 'Crispy breaded chicken cutlet', 245.00, 100.00, 'chicken_cutlet.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Chicken Wings', 'Crispy fried chicken wings', 165.00, 65.00, 'chicken_wings.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Salted Egg Chicken Wings', 'Crispy wings with salted egg coating', 195.00, 75.00, 'salted_egg_chicken_wings.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Honey Garlic Chicken', 'Sweet and savory glazed chicken', 225.00, 90.00, 'honey_garlic_chicken.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Lemongrass Chicken', 'Aromatic lemongrass-marinated chicken', 215.00, 85.00, 'lemongrass_chicken.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Soy Glazed Chicken', 'Tender chicken in sweet soy glaze', 205.00, 80.00, 'soy_glazed_chicken.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Popcorn Chicken', 'Bite-sized crispy chicken pieces', 175.00, 65.00, 'popcorn_chicken.jpg', false);
        $this->createMenuItem($alaCarteCategory, 'Black Pepper Beef', 'Tender beef with black pepper sauce', 225.00, 90.00, 'black_pepper_beef.jpg', false);

        // RICE MEALS
        $this->createMenuItem($riceMealsCategory, 'Sizzling Pork Sisig', 'Filipino sizzling pork dish with rice', 235.00, 95.00, 'sizzling_pork_sisig.jpg', false);
        $this->createMenuItem($riceMealsCategory, 'Sizzling Tofu Sisig', 'Vegetarian version of the classic sisig with rice', 185.00, 70.00, 'sizzling_tofu_sisig.jpg', false);

        // NOODLES AND PASTA
        $this->createMenuItem($noodlesPastaCategory, 'Asian Delight Pasta', 'Fusion pasta with Asian flavors', 235.00, 95.00, 'asian_delight_pasta.jpg', false);
        $this->createMenuItem($noodlesPastaCategory, 'Ban Mian Noodles', 'Handmade noodles in savory broth', 185.00, 70.00, 'ban_mian_noodles.jpg', false);
        $this->createMenuItem($noodlesPastaCategory, 'Pad Thai', 'Traditional Thai stir-fried noodles', 195.00, 75.00, 'pad_thai.jpg', false);
        $this->createMenuItem($noodlesPastaCategory, 'Creamy Meatball Spaghetti', 'Spaghetti with creamy meatball sauce', 215.00, 85.00, 'creamy_meatball_spaghetti.jpg', false);
        $this->createMenuItem($noodlesPastaCategory, 'Aglio e Olio Pasta', 'Simple pasta with garlic and olive oil', 165.00, 60.00, 'aglio_e_olio_pasta.jpg', false);

        // SALAD
        $this->createMenuItem($saladCategory, 'LPC Salad', 'Fresh mixed greens with house dressing', 125.00, 45.00, 'LPC_salad.jpg', false);

        // LIGHT MEALS
        $this->createMenuItem($lightMealsCategory, 'BG1 Sandwich', 'Hearty sandwich with premium ingredients', 155.00, 60.00, 'bg1_sandwich.jpg', false);
        $this->createMenuItem($lightMealsCategory, 'Kaya Toast', 'Traditional Malaysian coconut jam toast', 65.00, 25.00, 'kaya_toast.jpg', false);
        $this->createMenuItem($lightMealsCategory, 'Luncheon Meat Fritters', 'Crispy fried luncheon meat', 145.00, 55.00, 'luncheon_meat_fritters.jpg', false);
        $this->createMenuItem($lightMealsCategory, 'Vegetable Tempura', 'Lightly battered and fried vegetables', 155.00, 60.00, 'vegetable_tempura.jpg', false);
        $this->createMenuItem($lightMealsCategory, 'Spicy Creamy Yogurt', 'Creamy yogurt with spicy kick', 125.00, 45.00, 'spicy_creamy_yogurt.jpg', false);

        // APPETIZER
        $this->createMenuItem($appetizerCategory, 'Fish Fingers', 'Crispy breaded fish sticks', 135.00, 50.00, 'fish_fingers.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Fries', 'Golden crispy french fries', 85.00, 30.00, 'fries.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Garlic Green Beans', 'Stir-fried green beans with garlic', 95.00, 35.00, 'garlic_green_beans.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Loaded Potato Balls', 'Crispy potato balls with toppings', 125.00, 45.00, 'loaded_potato_balls.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Onion Rings', 'Crispy beer-battered onion rings', 105.00, 38.00, 'onion_rings.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Crab Fritters', 'Crispy crab meat fritters', 165.00, 65.00, 'crab_fritters.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Mixed Veggies', 'Healthy mix of fresh vegetables', 85.00, 32.00, 'mixed_veggies.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Steamed Rice Platter', 'Fluffy steamed jasmine rice', 45.00, 15.00, 'steamed_rice_platter.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Crispy Donburi', 'Japanese rice bowl with crispy toppings', 185.00, 70.00, 'Crispy_Donburi.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Butter Garlic Potato', 'Roasted potatoes with butter and garlic', 105.00, 38.00, 'butter_garlic_potato.jpg', false);
        $this->createMenuItem($appetizerCategory, 'Rice', 'Plain steamed rice', 35.00, 12.00, 'rice.jpg', false);

        // ALL DAY BREAKFAST
        $this->createMenuItem($allDayBreakfastCategory, 'Omelette', 'Fluffy eggs cooked to perfection', 75.00, 28.00, 'omelette.jpg', false);

        $this->command->info('All menu items seeded successfully with proper categories!');
    }

    private function createMenuItem($category, $name, $description, $price, $cost, $image, $hasVariants, $prepTime = 5)
    {
        if (!$category) {
            $this->command->error("Category not found for item: {$name}");
            return;
        }

        $menuItem = MenuItem::firstOrCreate([
            'name' => $name
        ], [
            'category_id' => $category->id,
            'description' => $description,
            'price' => $price,
            'cost' => $cost,
            'image' => $image,
            'has_variants' => $hasVariants,
            'preparation_time' => $prepTime,
        ]);

        // Add variants for drinks
        if ($hasVariants) {
            $this->createVariant($menuItem, 'Small', 0);
            $this->createVariant($menuItem, 'Medium', 15);
            $this->createVariant($menuItem, 'Large', 25);
        }

        // Create inventory
        $stockAmount = $hasVariants ? 100 : 50;
        $unit = $category->name === 'Coffee' ? 'cups' : 'pieces';
        $this->createInventory($menuItem, $stockAmount, 10, $stockAmount * 2, $unit);

        return $menuItem;
    }

    private function createVariant($menuItem, $name, $priceAdjustment)
    {
        MenuItemVariant::firstOrCreate([
            'menu_item_id' => $menuItem->id,
            'variant_name' => $name,
        ], [
            'variant_value' => $priceAdjustment,
        ]);
    }

    private function createInventory($menuItem, $currentStock, $minimumStock, $maximumStock, $unit)
    {
        Inventory::firstOrCreate([
            'menu_item_id' => $menuItem->id,
        ], [
            'current_stock' => $currentStock,
            'minimum_stock' => $minimumStock,
            'maximum_stock' => $maximumStock,
            'unit' => $unit,
        ]);
    }
}