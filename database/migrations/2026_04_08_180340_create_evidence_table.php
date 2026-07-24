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
        Schema::create('evidence', function (Blueprint $table) {
            $table->id('evidenceID');

            $table->unsignedBigInteger('complaintID');

            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');

            $table->dateTime('uploaded_time');

            $table->timestamps();

            $table->foreign('complaintID')
                ->references('complaintID')
                ->on('complaints')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
