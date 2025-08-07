<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;

class PointService
{
    // public function awardPoints(User $user, int $points, string $source, $sourceId = null)
    // {
    //     $user->increment('points_balance', $points);

    //     return PointTransaction::create([
    //         'user_id' => $user->id,
    //         'points' => $points,
    //         'type' => 'credit',
    //         'source' => $source,
    //         'source_id' => $sourceId,
    //         'balance_after' => $user->points_balance
    //     ]);
    // }

    // public function deductPoints(User $user, int $points, string $source, $sourceId = null)
    // {
    //     if ($user->points_balance < $points) {
    //         throw new \Exception('Insufficient points');
    //     }

    //     $user->decrement('points_balance', $points);

    //     return PointTransaction::create([
    //         'user_id' => $user->id,
    //         'points' => $points,
    //         'type' => 'debit',
    //         'source' => $source,
    //         'source_id' => $sourceId,
    //         'balance_after' => $user->points_balance
    //     ]);
    // }

    public function purchasePoints(User $user, int $packageId)
    {
        // Get package details
        $package = $this->getPackage($packageId);

        // Process payment
        $payment = $this->processPayment($user, $package['price']);

        // Award points
        return $this->awardPoints($user, $package['points'], 'purchase', $payment->id);
    }

    private function getPackage($packageId)
    {
        // Defined packages from documentation
        $packages = [
            1 => ['points' => 500, 'price' => 10],
            2 => ['points' => 5000, 'price' => 45],
            3 => ['points' => 10000, 'price' => 80]
        ];

        return $packages[$packageId] ?? null;
    }

    private function processPayment(User $user, $amount)
    {
        // Payment gateway integration would go here
        // Return payment transaction record
    }
    public function awardPoints(
        User $user,
        int $points,
        string $source,
        $sourceId = null,
        string $description = null
    ) {
        $user->increment('points_balance', $points);

        return PointTransaction::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => PointTransaction::TYPE_CREDIT,
            'source' => $source,
            'source_id' => $sourceId,
            'balance_before' => $user->points_balance - $points,
            'balance_after' => $user->points_balance,
            'description' => $description
        ]);
    }

    public function deductPoints(
        User $user,
        int $points,
        string $source,
        $sourceId = null,
        string $description = null
    ) {
        if ($user->points_balance < $points) {
            throw new \Exception('Insufficient points');
        }

        $user->decrement('points_balance', $points);

        return PointTransaction::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => PointTransaction::TYPE_DEBIT,
            'source' => $source,
            'source_id' => $sourceId,
            'balance_before' => $user->points_balance + $points,
            'balance_after' => $user->points_balance,
            'description' => $description
        ]);
    }

    public function purchasePoints(User $user, int $packageId, string $gateway)
    {
        $packages = [
            1 => ['points' => 500, 'price' => 10],
            2 => ['points' => 5000, 'price' => 45],
            3 => ['points' => 10000, 'price' => 80]
        ];

        if (!isset($packages[$packageId])) {
            throw new \Exception('Invalid package');
        }

        $package = $packages[$packageId];
        $paymentService = app(PaymentService::class);

        // Initiate payment
        $payment = $paymentService->initiatePayment(
            $user,
            $package['price'],
            $gateway,
            $package
        );

        return $payment;
    }

    public function handlePaymentSuccess(PaymentTransaction $transaction)
    {
        if ($transaction->status === PaymentTransaction::STATUS_COMPLETED) {
            return; // Already processed
        }

        $user = $transaction->user;
        $package = $transaction->package;

        // Award points
        $this->awardPoints(
            $user,
            $package['points'],
            'purchase',
            $transaction->id,
            "Point purchase - {$package['points']} points"
        );

        // Update payment status
        $transaction->update([
            'status' => PaymentTransaction::STATUS_COMPLETED,
            'points_awarded' => $package['points']
        ]);
    }
}
