<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * OPTION 1: If you want to add dedicated columns for Maya data
     * OPTION 2: Store Maya data in existing 'notes' column as JSON (recommended if you don't want to modify schema)
     * 
     * This migration adds dedicated columns for Maya QR payment data.
     * If you prefer to use the existing 'notes' column, you can skip this migration.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('orders', 'maya_payment_id')) {
                $table->string('maya_payment_id', 255)->nullable()->after('paymongo_payment_intent_id');
            }
            
            if (!Schema::hasColumn('orders', 'maya_qr_url')) {
                $table->text('maya_qr_url')->nullable()->after('maya_payment_id');
            }
            
            if (!Schema::hasColumn('orders', 'maya_reference_number')) {
                $table->string('maya_reference_number', 255)->nullable()->after('maya_qr_url');
            }
            
            if (!Schema::hasColumn('orders', 'verification_code')) {
                $table->string('verification_code', 20)->nullable()->after('maya_reference_number');
            }
            
            // Add index for verification code for faster lookups
            if (!Schema::hasColumn('orders', 'verification_code')) {
                $table->index('verification_code');
            }
            
            // Note: table_number already exists in your schema
            // Note: You already have paymongo_payment_intent_id which can be reused for maya_payment_id if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop indexes first
            if (Schema::hasColumn('orders', 'verification_code')) {
                $table->dropIndex(['verification_code']);
            }
            
            // Drop columns if they exist
            $columnsToCheck = [
                'maya_payment_id',
                'maya_qr_url',
                'maya_reference_number',
                'verification_code'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};