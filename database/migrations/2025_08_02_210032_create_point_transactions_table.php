<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('points');
            $table->enum('type', ['credit', 'debit']);
            $table->string('source'); // exam, blog, purchase, streak, etc.
            $table->unsignedBigInteger('source_id')->nullable(); // Related model ID
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
