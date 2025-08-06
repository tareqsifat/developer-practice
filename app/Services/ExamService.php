<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\User;
use Carbon\Carbon;

class ExamService extends BaseService
{
    protected function getModelClass(): string
    {
        return Exam::class;
    }

    public function startExam(User $user, $examId)
    {
        $exam = $this->find($examId);

        // Check if user can take this exam
        if (!$this->canTakeExam($user, $exam)) {
            throw new \Exception('You cannot start this exam');
        }

        // Deduct points if required
        if ($exam->price_in_points > 0) {
            $this->deductPoints($user, $exam->price_in_points);
        }

        return ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => Carbon::now(),
            'status' => 'in_progress'
        ]);
    }

    private function canTakeExam(User $user, Exam $exam): bool
    {
        // Check max attempts
        $attemptCount = $user->examAttempts()->where('exam_id', $exam->id)->count();
        if ($attemptCount >= $exam->max_attempts) {
            return false;
        }

        // Check if exam is active
        if (!$exam->is_active) {
            return false;
        }

        // Check time window
        $now = Carbon::now();
        if ($exam->start_time && $now->lt($exam->start_time)) {
            return false;
        }
        if ($exam->end_time && $now->gt($exam->end_time)) {
            return false;
        }

        // Check points balance
        if ($exam->price_in_points > 0 && $user->points_balance < $exam->price_in_points) {
            return false;
        }

        return true;
    }

    private function deductPoints(User $user, int $points)
    {
        $user->decrement('points_balance', $points);
        // Create point transaction record
    }

    public function submitExam(ExamAttempt $attempt, array $answers)
    {
        if ($attempt->status !== 'in_progress') {
            throw new \Exception('Exam attempt not in progress');
        }

        $exam = $attempt->exam;
        $totalMarks = 0;
        $correctAnswers = 0;

        foreach ($answers as $answer) {
            $question = $exam->questions()->findOrFail($answer['question_id']);

            $examAnswer = ExamAnswer::create([
                'exam_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $answer['selected_option_id'] ?? null,
                'answer_text' => $answer['answer_text'] ?? null,
            ]);

            // Auto-grade if possible
            if ($question->type === 'mcq') {
                $isCorrect = $question->correctOption &&
                    $answer['selected_option_id'] === $question->correctOption->id;

                $marks = $isCorrect ? $question->pivot->marks : 0;

                $examAnswer->update([
                    'is_correct' => $isCorrect,
                    'marks_awarded' => $marks
                ]);

                $totalMarks += $marks;
                if ($isCorrect) $correctAnswers++;
            }
        }

        // Calculate results
        $percentage = ($totalMarks / $exam->total_marks) * 100;
        $passed = $percentage >= $exam->passing_score;

        // Update attempt
        $attempt->update([
            'completed_at' => now(),
            'status' => 'completed',
            'total_questions' => count($answers),
            'attempted_questions' => count($answers),
            'correct_answers' => $correctAnswers,
            'marks_obtained' => $totalMarks,
            'percentage_score' => $percentage,
            'time_taken_seconds' => $attempt->started_at->diffInSeconds(now())
        ]);

        // Award points for completion
        app(PointService::class)->awardPoints(
            $attempt->user,
            100,
            'exam_completion',
            $attempt->id
        );

        return $attempt;
    }

}
