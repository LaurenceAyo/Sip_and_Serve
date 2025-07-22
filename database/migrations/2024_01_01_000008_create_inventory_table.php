<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            
            // Option 1: If menu_items table exists and has 'id' as primary key
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            
            // Option 2: If you need to specify the referenced column explicitly
            // $table->unsignedBigInteger('menu_item_id');
            // $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->integer('maximum_stock')->default(1000);
            $table->string('unit', 50)->default('pieces');
            $table->timestamp('last_restocked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};