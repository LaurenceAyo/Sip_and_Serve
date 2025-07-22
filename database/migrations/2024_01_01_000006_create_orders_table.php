<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Fix: Add the allowed enum values
            $table->enum('payment_method', ['cash', 'E_wallet'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            
            $table->text('notes')->nullable();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('kitchen_received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};