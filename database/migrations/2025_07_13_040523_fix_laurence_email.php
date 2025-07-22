<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix the invalid email for Laurence Ayo
        $user = User::where('email', 'laurenceayo7.com')->first();
        
        if ($user) {
            $user->email = 'laurenceayo7@gmail.com';
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the email change
        $user = User::where('email', 'laurenceayo7@gmail.com')->first();
        
        if ($user) {
            $user->email = 'laurenceayo7.com';
            $user->save();
        }
    }
};