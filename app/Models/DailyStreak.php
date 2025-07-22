<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'current_streak',
        'login',
        'exam_participated',
        'question_submitted',
        'blog_posted',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function recordActivity(User $user, $activityType)
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $todayStreak = self::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        $yesterdayStreak = self::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        // Set activity flag
        $todayStreak->{$activityType} = true;

        // Calculate streak
        if ($yesterdayStreak) {
            $todayStreak->current_streak = $yesterdayStreak->current_streak + 1;
        } else {
            $todayStreak->current_streak = 1;
        }

        $todayStreak->save();
        return $todayStreak;
    }

    public function getActivitiesAttribute()
    {
        return [
            'login' => $this->login,
            'exam_participated' => $this->exam_participated,
            'question_submitted' => $this->question_submitted,
            'blog_posted' => $this->blog_posted,
        ];
    }
}
