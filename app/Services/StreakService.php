<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyStreak;
use Carbon\Carbon;

class StreakService
{
    public function recordActivity(User $user, string $activityType)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $streak = DailyStreak::firstOrNew([
            'user_id' => $user->id,
            'date' => $today
        ]);

        // Check if user had activity yesterday
        $previousStreak = DailyStreak::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        if ($previousStreak) {
            $streak->current_streak = $previousStreak->current_streak + 1;
        } else {
            $streak->current_streak = 1;
        }

        // Record activity type
        $streak->{$activityType} = true;
        $streak->save();

        // Check for streak milestones
        $this->checkMilestones($user, $streak->current_streak);

        return $streak;
    }

    private function checkMilestones(User $user, int $currentStreak)
    {
        $milestones = [7, 30, 90, 365];
        $rewards = [
            7 => ['points' => 50],
            30 => ['points' => 200, 'badge' => '30-day-streak'],
            90 => ['points' => 500, 'features' => ['premium_access']],
            365 => ['points' => 2000, 'recognition' => 'elite-member']
        ];

        if (in_array($currentStreak, $milestones)) {
            $this->awardStreakBonus($user, $rewards[$currentStreak]);
        }
    }

    private function awardStreakBonus(User $user, array $bonus)
    {
        // Award points
        if (isset($bonus['points'])) {
            app(PointService::class)->awardPoints($user, $bonus['points'], 'streak');
        }

        // Award badges or features
        if (isset($bonus['badge'])) {
            $user->badges()->create(['name' => $bonus['badge']]);
        }

        // ... other rewards
    }
        public function recordActivity(User $user, string $activityType)
    {
        $today = Carbon::today();

        // Get or create today's streak
        $streak = DailyStreak::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today
        ]);

        // Set activity flag
        $streak->{$activityType} = true;

        // Check if user had activity yesterday
        $yesterday = $today->copy()->subDay();
        $yesterdayStreak = DailyStreak::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        // Calculate streak
        if ($yesterdayStreak) {
            $streak->current_streak = $yesterdayStreak->current_streak + 1;
        } else {
            $streak->current_streak = max(1, $streak->current_streak);
        }

        $streak->save();

        // Check for streak milestones
        $this->checkMilestones($user, $streak->current_streak);

        return $streak;
    }

    public function checkAndResetStreak(User $user, Carbon $date)
    {
        $streak = DailyStreak::firstOrCreate([
            'user_id' => $user->id,
            'date' => $date
        ]);

        $previousDay = $date->copy()->subDay();
        $previousStreak = DailyStreak::where('user_id', $user->id)
            ->where('date', $previousDay)
            ->first();

        if (!$streak->hasActivity() && $previousStreak) {
            // User missed a day, reset streak
            $streak->current_streak = 1;
            $streak->save();
        }
    }

    private function checkMilestones(User $user, int $currentStreak)
    {
        $milestones = [7, 30, 90, 365];
        $rewards = [
            7 => ['points' => 50],
            30 => ['points' => 200, 'badge' => '30-day-streak'],
            90 => ['points' => 500, 'features' => ['premium_access']],
            365 => ['points' => 2000, 'recognition' => 'elite-member']
        ];

        if (in_array($currentStreak, $milestones)) {
            $this->awardStreakBonus($user, $rewards[$currentStreak]);
        }
    }

    private function awardStreakBonus(User $user, array $bonus)
    {
        $pointService = app(PointService::class);

        if (isset($bonus['points'])) {
            $pointService->awardPoints(
                $user,
                $bonus['points'],
                'streak_bonus',
                null,
                "{$bonus['points']}-day streak bonus"
            );
        }

        // Handle other rewards (badges, features, etc.)
    }

    public function getCurrentStreak(User $user)
    {
        return DailyStreak::where('user_id', $user->id)
            ->orderByDesc('date')
            ->first();
    }
}
