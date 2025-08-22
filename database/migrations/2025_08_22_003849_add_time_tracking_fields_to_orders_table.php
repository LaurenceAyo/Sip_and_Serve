<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeTrackingFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add estimated preparation time in minutes
            $table->integer('estimated_prep_time')->nullable()->after('status');
            
            // Add table number for dine-in orders
            $table->string('table_number', 10)->nullable()->after('order_type');
            
            // Make sure these datetime fields exist
            if (!Schema::hasColumn('orders', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('kitchen_received_at');
            }
            
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_prep_time',
                'table_number'
            ]);
        });
    }
}