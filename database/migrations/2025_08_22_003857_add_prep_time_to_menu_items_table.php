<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPrepTimeToMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Add preparation time in minutes for each menu item
            $table->integer('prep_time_minutes')->default(5)->after('price');
        });
        
        // Update existing menu items with realistic prep times
        DB::table('menu_items')->update([
            'prep_time_minutes' => DB::raw("
                CASE 
                    WHEN LOWER(name) LIKE '%coffee%' OR LOWER(name) LIKE '%espresso%' THEN 3
                    WHEN LOWER(name) LIKE '%latte%' OR LOWER(name) LIKE '%cappuccino%' OR LOWER(name) LIKE '%americano%' THEN 4
                    WHEN LOWER(name) LIKE '%frappuccino%' OR LOWER(name) LIKE '%smoothie%' THEN 6
                    WHEN LOWER(name) LIKE '%sandwich%' OR LOWER(name) LIKE '%burger%' THEN 8
                    WHEN LOWER(name) LIKE '%pasta%' OR LOWER(name) LIKE '%rice%' THEN 12
                    WHEN LOWER(name) LIKE '%cake%' OR LOWER(name) LIKE '%dessert%' THEN 2
                    WHEN LOWER(name) LIKE '%salad%' THEN 5
                    WHEN LOWER(name) LIKE '%soup%' THEN 7
                    ELSE 5
                END
            ")
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('prep_time_minutes');
        });
    }
}