<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('cashier')->after('manager_id');
            $table->string('status')->default('active')->after('role');
            $table->json('permissions')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('permissions');
            $table->unsignedBigInteger('created_by')->nullable()->after('last_login_at');
            $table->boolean('password_reset_required')->default(false)->after('created_by');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 
                'status', 
                'permissions', 
                'last_login_at', 
                'created_by', 
                'password_reset_required'
            ]);
        });
    }
};