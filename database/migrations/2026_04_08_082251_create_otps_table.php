<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id('otpID');
            $table->unsignedBigInteger('touristID');
            $table->string('otp_code');
            $table->dateTime('expiry_time');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('touristID')->references('touristID')->on('tourists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
