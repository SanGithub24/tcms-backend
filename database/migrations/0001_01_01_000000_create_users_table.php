<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('userID');

            $table->string('user_type'); 

            $table->string('full_name');

            $table->string('email')->unique();

            $table->string('phone_number');

            $table->string('badge_number')->unique();

            $table->string('district');

            $table->string('password');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};