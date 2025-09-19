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
            $table->json('data_included')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_settings');
    }
};