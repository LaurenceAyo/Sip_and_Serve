<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->boolean('auto_backup_enabled')->default(false)->after('backup_schedule');
            $table->timestamp('next_backup_at')->nullable()->after('auto_backup_enabled');
            $table->timestamp('last_backup_at')->nullable()->after('next_backup_at');
        });
    }

    public function down()
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->dropColumn(['auto_backup_enabled', 'next_backup_at', 'last_backup_at']);
        });
    }
};