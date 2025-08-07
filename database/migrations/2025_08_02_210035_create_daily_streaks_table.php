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
        Schema::create('daily_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('date');
            $table->integer('current_streak')->default(0);
            $table->boolean('login')->default(false);
            $table->boolean('exam_participated')->default(false);
            $table->boolean('question_submitted')->default(false);
            $table->boolean('blog_posted')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_streaks');
    }
};
