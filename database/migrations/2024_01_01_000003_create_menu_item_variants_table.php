<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menu_item_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
    $table->string('variant_name'); // e.g., size, color, etc.
    $table->string('variant_value'); // e.g., small, medium, large
    $table->decimal('price_adjustment', 8, 2)->default(0.00);
    $table->timestamps();
});
    }

    public function down()
    {
        Schema::dropIfExists('menu_item_variants');
    }
};