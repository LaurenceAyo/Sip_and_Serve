<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'maya_reference')) {
                $table->string('maya_reference')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('orders', 'maya_payment_data')) {
                $table->text('maya_payment_data')->nullable()->after('maya_reference');
            }
            
            if (!Schema::hasColumn('orders', 'maya_webhook_received_at')) {
                $table->timestamp('maya_webhook_received_at')->nullable()->after('maya_payment_data');
            }
            
            if (!Schema::hasColumn('orders', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('maya_webhook_received_at');
            }
        });

        // Maya payment logs
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
            $table->dropColumn([
                'maya_reference', 
                'maya_payment_data', 
                'maya_webhook_received_at',
                'confirmed_by'
            ]);
        });

        Schema::dropIfExists('maya_payment_logs');
    }
};