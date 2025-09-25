<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('backup_location', ['local', 'server'])->default('local');
            $table->enum('backup_schedule', ['weekly', 'monthly'])->default('weekly');
            $table->timestamp('last_backup_at')->nullable();
            $table->timestamp('next_backup_at')->nullable();
            $table->boolean('auto_backup_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_settings');
    }
};
