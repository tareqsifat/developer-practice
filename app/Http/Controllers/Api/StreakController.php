<?php

// app/Http/Controllers/Api/StreakController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StreakService;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    protected $streakService;

    public function __construct(StreakService $streakService)
    {
        $this->streakService = $streakService;
    }

    public function index(Request $request)
    {
        $streaks = $request->user()
            ->dailyStreaks()
            ->orderByDesc('date')
            ->paginate(30);

        return response()->json($streaks);
    }

    public function current(Request $request)
    {
        $streak = $this->streakService->getCurrentStreak($request->user());
        return response()->json($streak);
    }
}

