<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('reviewID');

            $table->unsignedBigInteger('complaintID');
            $table->unsignedBigInteger('touristID');

            $table->unsignedTinyInteger('rating');
            $table->text('description');

            $table->string('status')->default('Active');

            $table->timestamps();

            $table->foreign('complaintID')
                ->references('complaintID')
                ->on('complaints')
                ->onDelete('cascade');

            $table->foreign('touristID')
                ->references('touristID')
                ->on('tourists')
                ->onDelete('cascade');

            $table->unique('complaintID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};