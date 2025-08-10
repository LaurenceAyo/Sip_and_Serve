<?php
// database/migrations/xxxx_xx_xx_add_paymongo_fields_to_orders_table_corrected.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add order_number after id (if it doesn't exist)
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable()->after('id');
            }
            
            // Add order_type after order_number (if it doesn't exist)
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type')->default('dine-in')->after('order_number');
            }
            
            // Add PayMongo fields after payment_status
            if (!Schema::hasColumn('orders', 'paymongo_payment_intent_id')) {
                $table->string('paymongo_payment_intent_id')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('orders', 'paymongo_payment_method_id')) {
                $table->string('paymongo_payment_method_id')->nullable()->after('paymongo_payment_intent_id');
            }
            
            // Add cash fields after total_amount
            if (!Schema::hasColumn('orders', 'cash_amount')) {
                $table->decimal('cash_amount', 10, 2)->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'change_amount')) {
                $table->decimal('change_amount', 10, 2)->nullable()->after('cash_amount');
            }
            
            // Add paid_at after updated_at (since completed_at doesn't exist)
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('updated_at');
            }
            
            // Add kitchen timestamps if they don't exist
            if (!Schema::hasColumn('orders', 'kitchen_received_at')) {
                $table->timestamp('kitchen_received_at')->nullable()->after('paid_at');
            }
            
            if (!Schema::hasColumn('orders', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('kitchen_received_at');
            }
            
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });

        // Add indexes separately to avoid conflicts
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('paymongo_payment_intent_id');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore error
        }
        
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('order_number');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore error
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop indexes first (with try-catch to handle if they don't exist)
            try {
                $table->dropIndex(['paymongo_payment_intent_id']);
            } catch (\Exception $e) {
                // Index might not exist, ignore error
            }
            
            try {
                $table->dropIndex(['order_number']);
            } catch (\Exception $e) {
                // Index might not exist, ignore error
            }
            
            // Drop columns only if they exist
            $columnsToDrop = [];
            
            if (Schema::hasColumn('orders', 'order_number')) {
                $columnsToDrop[] = 'order_number';
            }
            
            if (Schema::hasColumn('orders', 'order_type')) {
                $columnsToDrop[] = 'order_type';
            }
            
            if (Schema::hasColumn('orders', 'paymongo_payment_intent_id')) {
                $columnsToDrop[] = 'paymongo_payment_intent_id';
            }
            
            if (Schema::hasColumn('orders', 'paymongo_payment_method_id')) {
                $columnsToDrop[] = 'paymongo_payment_method_id';
            }
            
            if (Schema::hasColumn('orders', 'cash_amount')) {
                $columnsToDrop[] = 'cash_amount';
            }
            
            if (Schema::hasColumn('orders', 'change_amount')) {
                $columnsToDrop[] = 'change_amount';
            }
            
            if (Schema::hasColumn('orders', 'paid_at')) {
                $columnsToDrop[] = 'paid_at';
            }
            
            if (Schema::hasColumn('orders', 'kitchen_received_at')) {
                $columnsToDrop[] = 'kitchen_received_at';
            }
            
            if (Schema::hasColumn('orders', 'started_at')) {
                $columnsToDrop[] = 'started_at';
            }
            
            if (Schema::hasColumn('orders', 'completed_at')) {
                $columnsToDrop[] = 'completed_at';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};