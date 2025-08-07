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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stack_id')->constrained('stacks')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['mcq', 'short_answer', 'long_answer'])->default('mcq');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('marks')->default(1);
            $table->integer('time_limit_seconds')->nullable();
            $table->text('explanation')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['stack_id', 'subject_id', 'topic_id']);
            $table->index(['is_approved', 'difficulty']);
            $table->index(['user_id', 'is_approved']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

