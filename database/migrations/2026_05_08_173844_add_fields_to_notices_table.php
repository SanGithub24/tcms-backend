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
        Schema::table('notices', function (Blueprint $table) {

            $table->string('category')->default('General');

            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('low');

            $table->string('location')->nullable();

            $table->boolean('is_featured')->default(false);

            $table->timestamp('expires_at')->nullable();

            $table->string('image')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            //
        });
    }
};
