<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        return response()->json($this->dashboardService->getUserDashboard($userId));
    }

    public function statistics(Request $request)
    {
        $userId = $request->user()->id;
        return response()->json($this->dashboardService->getUserStatistics($userId));
    }

    public function recentActivity(Request $request)
    {
        $userId = $request->user()->id;
        $limit = $request->input('limit', 10);
        return response()->json($this->dashboardService->getRecentActivity($userId, $limit));
    }

    public function recommendations(Request $request)
    {
        $userId = $request->user()->id;
        return response()->json($this->dashboardService->getRecommendations($userId));
    }
}
