<?php

// app/Http/Controllers/Api/PointController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends Controller
{
    protected $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        $transactions = $request->user()
            ->pointTransactions()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($transactions);
    }

    public function purchasePoints(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|min:1|max:3',
            'gateway' => 'required|in:bkash,nagad,sslcommerz'
        ]);

        $transaction = $this->pointService->purchasePoints(
            $request->user(),
            $request->package_id,
            $request->gateway
        );

        return response()->json($transaction, 201);
    }

    public function rewardPoints(Request $request)
    {
        // Only admins can manually reward points
        $this->authorize('reward-points');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string'
        ]);

        $transaction = $this->pointService->awardPoints(
            User::find($request->user_id),
            $request->points,
            'admin_reward',
            null,
            $request->reason
        );

        return response()->json($transaction, 201);
    }
}

// Add to routes/api.php
