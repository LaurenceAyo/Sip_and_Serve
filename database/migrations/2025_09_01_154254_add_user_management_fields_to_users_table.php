<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('cashier');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->text('permissions')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_reset_required')) {
                $table->boolean('password_reset_required')->default(false);
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['role', 'status', 'permissions', 'password_reset_required', 'last_login_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};