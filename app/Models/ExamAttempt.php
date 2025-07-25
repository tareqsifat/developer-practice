<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'completed_at',
        'status',
        'total_questions',
        'attempted_questions',
        'correct_answers',
        'incorrect_answers',
        'marks_obtained',
        'percentage_score',
        'time_taken_seconds',
        'result_data',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'result_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function calculateScore()
    {
        $correct = $this->answers()->where('is_correct', true)->count();
        $total = $this->answers()->count();

        $this->correct_answers = $correct;
        $this->incorrect_answers = $total - $correct;
        $this->percentage_score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
        $this->save();
    }

    public function getDurationAttribute()
    {
        if ($this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return now()->diffInSeconds($this->started_at);
    }

    public function isPassed()
    {
        if (!$this->exam || !$this->exam->passing_score) {
            return false;
        }
        return $this->percentage_score >= $this->exam->passing_score;
    }
}
