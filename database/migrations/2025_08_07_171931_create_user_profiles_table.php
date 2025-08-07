<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration for the user_profiles table.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->string('experience_level', 50)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();

            // Add index for faster user lookups
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
