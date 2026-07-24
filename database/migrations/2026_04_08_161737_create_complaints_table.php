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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaintID');

            $table->unsignedBigInteger('touristID');
            $table->unsignedBigInteger('locationID');

            $table->string('category');
            $table->text('description');

            $table->date('incident_date');
            $table->date('complaint_date');

            $table->text('police_note')->nullable();

            $table->string('status')->default('Pending');

            $table->timestamps();

            $table->foreign('touristID')
                ->references('touristID')
                ->on('tourists')
                ->onDelete('cascade');

            $table->foreign('locationID')
                ->references('locationID')
                ->on('locations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
