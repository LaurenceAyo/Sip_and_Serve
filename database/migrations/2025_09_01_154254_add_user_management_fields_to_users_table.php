<?php
// Create migration: php artisan make:migration add_user_management_fields_to_users_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserManagementFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('password_reset_required')->default(false);
            $table->timestamp('last_login_at')->nullable()->after('password_reset_required');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'permissions', 'password_reset_required', 'last_login_at']);
        });
    }
}
