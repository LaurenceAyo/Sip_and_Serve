<?php
// database/migrations/xxxx_xx_xx_create_pos_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->string('payment_intent_id')->unique();
            $table->string('payment_method_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('customer_name')->nullable();
            $table->enum('status', [
                'awaiting_payment_method',
                'awaiting_next_action',
                'processing',
                'succeeded',
                'failed',
                'cancelled'
            ])->default('awaiting_payment_method');
            $table->string('payment_method_type')->default('gcash');
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index('payment_method_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};