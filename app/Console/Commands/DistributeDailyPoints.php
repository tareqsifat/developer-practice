<?php

// app/Console/Commands/DistributeDailyPoints.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PointService;

class DistributeDailyPoints extends Command
{
    protected $signature = 'points:distribute-daily';
    protected $description = 'Distribute daily login points to active users';

    public function handle(PointService $pointService)
    {
        $users = User::where('is_active', true)
            ->whereDate('last_login_at', today())
            ->cursor();

        foreach ($users as $user) {
            $pointService->awardPoints($user, 300, 'daily_login');
        }

        $this->info("Daily points distributed to {$users->count()} users");
    }
}

// Register in app/Console/Kernel.php
// protected function schedule(Schedule $schedule)
// {
//     $schedule->command('points:distribute-daily')->dailyAt('04:00');
// }
