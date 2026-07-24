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
        Schema::create('complaint_assignments', function (Blueprint $table) {
            $table->id('assignmentID');

            $table->unsignedBigInteger('complaintID');

            $table->unsignedBigInteger('userID_police');
            // $table->unsignedBigInteger('assigned_by_admin');
            $table->unsignedBigInteger('assigned_by_admin')->nullable();

            $table->dateTime('assigned_at');

            $table->string('assignment_type');
            $table->string('assignment_status')->default('active');

            $table->timestamps();

            $table->foreign('complaintID')
                ->references('complaintID')
                ->on('complaints')
                ->onDelete('cascade');

            $table->foreign('userID_police')
                ->references('userID')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('assigned_by_admin')
                ->references('userID')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_assignments');
    }
};
