<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExamAttempt;
use App\Models\BlogPost;
use App\Models\PointTransaction;
use App\Models\DailyStreak;
use Carbon\Carbon;

class DashboardService
{
    public function getUserDashboard($userId)
    {
        $user = User::with('profile')->findOrFail($userId);

        return [
            'user' => $user,
            'statistics' => $this->getUserStatistics($userId),
            'recent_activity' => $this->getRecentActivity($userId, 5),
            'recommendations' => $this->getRecommendations($userId),
            'streak' => $this->getCurrentStreak($userId),
            'point_balance' => $user->points_balance,
        ];
    }

    public function getUserStatistics($userId)
    {
        return [
            'total_exams_taken' => ExamAttempt::where('user_id', $userId)->count(),
            'completed_exams' => ExamAttempt::where('user_id', $userId)
                ->where('status', 'completed')->count(),
            'average_score' => ExamAttempt::where('user_id', $userId)
                ->where('status', 'completed')
                ->avg('percentage_score') ?? 0,
            'questions_contributed' => $user->questions()->count(),
            'approved_questions' => $user->questions()->where('is_approved', true)->count(),
            'blog_posts' => BlogPost::where('user_id', $userId)->count(),
            'published_posts' => BlogPost::where('user_id', $userId)
                ->where('status', 'published')->count(),
            'streak_days' => DailyStreak::where('user_id', $userId)
                ->max('current_streak') ?? 0,
        ];
    }

    public function getRecentActivity($userId, $limit = 10)
    {
        $examAttempts = ExamAttempt::where('user_id', $userId)
            ->with('exam')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($attempt) {
                return [
                    'type' => 'exam_attempt',
                    'title' => $attempt->exam->title,
                    'score' => $attempt->percentage_score,
                    'status' => $attempt->status,
                    'date' => $attempt->created_at,
                ];
            });

        $pointTransactions = PointTransaction::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'point_transaction',
                    'points' => $transaction->points,
                    'source' => $transaction->source,
                    'balance_after' => $transaction->balance_after,
                    'date' => $transaction->created_at,
                ];
            });

        $blogActivities = BlogPost::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'blog_post',
                    'title' => $post->title,
                    'status' => $post->status,
                    'date' => $post->created_at,
                ];
            });

        // Combine and sort all activities
        return collect()
            ->merge($examAttempts)
            ->merge($pointTransactions)
            ->merge($blogActivities)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }

    public function getRecommendations($userId)
    {
        $user = User::findOrFail($userId);

        // Get weak areas based on exam performance
        $weakStacks = ExamAttempt::where('user_id', $userId)
            ->with('exam.stack')
            ->where('status', 'completed')
            ->get()
            ->groupBy('exam.stack_id')
            ->map(function ($attempts, $stackId) {
                return [
                    'stack_id' => $stackId,
                    'average_score' => $attempts->avg('percentage_score'),
                    'stack_name' => $attempts->first()->exam->stack->name,
                ];
            })
            ->sortBy('average_score')
            ->take(3)
            ->values();

        // Get recommended exams
        $recommendedExams = Exam::whereIn('stack_id', $weakStacks->pluck('stack_id'))
            ->where('is_active', true)
            ->whereNotIn('id', $user->examAttempts()->pluck('exam_id'))
            ->limit(3)
            ->get();

        // Get recommended blog posts
        $recommendedBlogs = BlogPost::whereIn('stack_id', $weakStacks->pluck('stack_id'))
            ->where('status', 'published')
            ->orderByDesc('views')
            ->limit(3)
            ->get();

        return [
            'weak_areas' => $weakStacks,
            'recommended_exams' => $recommendedExams,
            'recommended_blogs' => $recommendedBlogs,
        ];
    }

    public function getCurrentStreak($userId)
    {
        return DailyStreak::where('user_id', $userId)
            ->orderByDesc('date')
            ->first();
    }
}
