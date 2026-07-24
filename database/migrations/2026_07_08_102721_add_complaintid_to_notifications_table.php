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
        Schema::table('notifications', function (Blueprint $table) {

            $table->unsignedBigInteger('complaintID')->nullable()->after('userID');

            $table->foreign('complaintID')
                ->references('complaintID')
                ->on('complaints')
                ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {

            $table->dropForeign(['complaintID']);

            $table->dropColumn('complaintID');

        });
    }
};
