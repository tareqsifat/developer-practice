<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'stack_id',
        'subject_id',
        'topic_id',
        'question_text',
        'type',
        'difficulty',
        'marks',
        'time_limit_seconds',
        'explanation',
        'is_approved',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'marks' => 'integer',
        'time_limit_seconds' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stack()
    {
        return $this->belongsTo(Stack::class, 'stack_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class, 'question_id')->where('is_correct', true);
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }

    public function feedback()
    {
        return $this->hasMany(UserFeedback::class, 'question_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false)->whereNull('rejection_reason');
    }

    public function scopeRejected($query)
    {
        return $query->where('is_approved', false)->whereNotNull('rejection_reason');
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStack($query, $stackId)
    {
        return $query->where('stack_id', $stackId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    // Accessors
    public function getFormattedTimeAttribute()
    {
        if (!$this->time_limit_seconds) {
            return 'No time limit';
        }

        $minutes = floor($this->time_limit_seconds / 60);
        $seconds = $this->time_limit_seconds % 60;

        if ($minutes > 0) {
            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        }

        return "{$seconds}s";
    }

    public function getStatusAttribute()
    {
        if ($this->is_approved) {
            return 'approved';
        }

        if ($this->rejection_reason) {
            return 'rejected';
        }

        return 'pending';
    }

    public function getAverageRatingAttribute()
    {
        return $this->feedback()
                   ->whereNotNull('rating')
                   ->avg('rating') ?? 0;
    }

    public function getFeedbackCountAttribute()
    {
        return $this->feedback()->count();
    }

    // Helper Methods
    public function approve($approvedBy)
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject($reason, $rejectedBy = null)
    {
        $this->update([
            'is_approved' => false,
            'rejection_reason' => $reason,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function getCorrectAnswer()
    {
        if ($this->type === 'mcq') {
            return $this->correctOption;
        }

        // For short_answer and long_answer, return the explanation as the model answer
        return $this->explanation;
    }

    public function calculateScore($userAnswer)
    {
        if ($this->type === 'mcq') {
            $correctOption = $this->correctOption;
            if ($correctOption && $userAnswer == $correctOption->id) {
                return $this->marks;
            }
            return 0;
        }

        // For text-based answers, manual grading would be required
        // This is a simplified implementation
        return 0;
    }

    public function getUsageStatistics()
    {
        $totalAttempts = $this->answers()->count();
        $correctAttempts = 0;

        if ($this->type === 'mcq') {
            $correctOptionId = $this->correctOption?->id;
            if ($correctOptionId) {
                $correctAttempts = $this->answers()
                                      ->where('selected_option_id', $correctOptionId)
                                      ->count();
            }
        }

        return [
            'total_attempts' => $totalAttempts,
            'correct_attempts' => $correctAttempts,
            'success_rate' => $totalAttempts > 0 ? ($correctAttempts / $totalAttempts) * 100 : 0,
            'average_rating' => $this->average_rating,
            'feedback_count' => $this->feedback_count,
        ];
    }
}

