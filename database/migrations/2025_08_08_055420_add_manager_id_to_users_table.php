<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('manager_id', 10)->unique()->nullable();
        });

        // Generate unique manager IDs for existing users
        $users = User::all();
        foreach ($users as $user) {
            $user->manager_id = $this->generateUniqueManagerId();
            $user->save();
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('manager_id');
        });
    }

    private function generateUniqueManagerId()
    {
        do {
            $managerId = 'MNG' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('manager_id', $managerId)->exists());
        
        return $managerId;
    }
};