<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'cashier', 'kitchen'])->default('cashier')->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->json('permissions')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('permissions');
            $table->boolean('password_reset_required')->default(false)->after('last_login_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'permissions', 'last_login_at', 'password_reset_required']);
        });
    }
}