<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'stack_id',
        'subject_id',
        'topic_id',
        'duration_minutes',
        'total_marks',
        'passing_score',
        'max_attempts',
        'is_active',
        'price_in_points',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function stack()
    {
        return $this->belongsTo(Stack::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot('marks', 'order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
