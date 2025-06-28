<?php

// app/Console/Commands/CalculateDailyStreaks.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\StreakService;
use Carbon\Carbon;

class CalculateDailyStreaks extends Command
{
    protected $signature = 'streaks:calculate {date?}';
    protected $description = 'Calculate daily streaks for all active users';

    public function handle(StreakService $streakService)
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();

        $users = User::where('is_active', true)->cursor();

        foreach ($users as $user) {
            $streakService->checkAndResetStreak($user, $date);
        }

        $this->info("Streaks calculated for {$date->format('Y-m-d')}");
    }
}

// // Register in app/Console/Kernel.php
// protected function schedule(Schedule $schedule)
// {
//     $schedule->command('streaks:calculate')->dailyAt('03:00');
// }
