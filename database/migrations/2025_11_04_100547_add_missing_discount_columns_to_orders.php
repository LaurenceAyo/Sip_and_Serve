<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add only the columns that don't exist
            if (!Schema::hasColumn('orders', 'discount_type')) {
                $table->enum('discount_type', ['none', 'senior_citizen', 'pwd'])
                      ->default('none')
                      ->after('discount_amount');
            }
            
            if (!Schema::hasColumn('orders', 'discount_id_number')) {
                $table->string('discount_id_number', 50)
                      ->nullable()
                      ->after('discount_type');
            }
            
            if (!Schema::hasColumn('orders', 'amount_before_discount')) {
                $table->decimal('amount_before_discount', 10, 2)
                      ->nullable()
                      ->after('discount_id_number');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('orders', 'discount_id_number')) {
                $table->dropColumn('discount_id_number');
            }
            if (Schema::hasColumn('orders', 'amount_before_discount')) {
                $table->dropColumn('amount_before_discount');
            }
        });
    }
};