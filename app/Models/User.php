<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'email_verified_at',
        'avatar',
        'bio',
        'github_username',
        'linkedin_url',
        'website_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'user_id');
    }

    public function feedback()
    {
        return $this->hasMany(UserFeedback::class, 'user_id');
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'user_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Accessors & Mutators
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function getIsContributorAttribute()
    {
        return in_array($this->role, ['admin', 'contributor']);
    }

    // Helper Methods
    public function canTakeExam($examId)
    {
        $exam = Exam::find($examId);
        if (!$exam) {
            return false;
        }

        $attemptCount = $this->examAttempts()
                            ->where('exam_id', $examId)
                            ->count();

        return $attemptCount < $exam->max_attempts;
    }

    public function getSkillLevel($stackId = null)
    {
        $query = $this->examAttempts()
                     ->whereHas('exam', function ($q) {
                         $q->where('is_active', true);
                     })
                     ->where('status', 'completed');

        if ($stackId) {
            $query->whereHas('exam', function ($q) use ($stackId) {
                $q->where('stack_id', $stackId);
            });
        }

        $attempts = $query->get();

        if ($attempts->isEmpty()) {
            return 'beginner';
        }

        $averageScore = $attempts->avg('percentage_score');

        if ($averageScore >= 80) {
            return 'expert';
        } elseif ($averageScore >= 60) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    public function getRecommendedExams($limit = 5)
    {
        // Get user's weakest areas based on exam performance
        $weakAreas = $this->examAttempts()
                         ->with('exam.stack')
                         ->where('status', 'completed')
                         ->get()
                         ->groupBy('exam.stack_id')
                         ->map(function ($attempts) {
                             return $attempts->avg('percentage_score');
                         })
                         ->sortBy(function ($score) {
                             return $score;
                         })
                         ->take(3)
                         ->keys();

        return Exam::whereIn('stack_id', $weakAreas)
                   ->where('is_active', true)
                   ->whereNotIn('id', $this->examAttempts()->pluck('exam_id'))
                   ->limit($limit)
                   ->get();
    }

    public function interviewParticipations()
    {
        return $this->belongsToMany(Interview::class, 'interview_participants')
                    ->using(InterviewParticipant::class)
                    ->withPivot('joined_at', 'left_at', 'role')
                    ->withTimestamps();
    }
}

