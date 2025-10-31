<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Only add fields that don't exist
            if (!Schema::hasColumn('orders', 'maya_reference')) {
                $table->string('maya_reference')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('orders', 'maya_payment_data')) {
                $table->text('maya_payment_data')->nullable()->after('maya_reference');
            }
        });

        // Create Maya payment logs table for tracking webhooks
        if (!Schema::hasTable('maya_payment_logs')) {
            Schema::create('maya_payment_logs', function (Blueprint $table) {
                $table->id();
                $table->string('reference_number')->index();
                $table->decimal('amount', 10, 2)->nullable();
                $table->string('status')->nullable();
                $table->json('payload');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['maya_reference', 'maya_payment_data']);
        });

        Schema::dropIfExists('maya_payment_logs');
    }
};