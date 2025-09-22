<?php

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
        Schema::create('cash_drawer_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('drawer_number')->default(1);
            $table->string('action'); // 'manual_open', 'order_payment', etc.
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['action', 'created_at']);
            $table->index(['drawer_number', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_logs');
    }
};