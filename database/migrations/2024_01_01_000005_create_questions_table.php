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
            // Who created the question
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Exam category (HSC, SSC, Medical, IELTS, BCS)
            $table->foreignId('stack_id')->constrained('stacks')->onDelete('cascade');

            // Clean subject name (English, Bangla, Math)
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');

            // Sub-topic (English Grammar, Algebra, Trigonometry)
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');

            // Main question
            $table->text('question_text');

            // Type
            $table->enum('type', ['mcq', 'short_answer', 'long_answer'])->default('mcq');

            // Difficulty
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');

            // Marks & exam-time
            $table->integer('marks')->default(1);
            $table->integer('time_limit_seconds')->nullable();

            // Explanation / reasoning
            $table->text('explanation')->nullable();

            // Approval
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
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

