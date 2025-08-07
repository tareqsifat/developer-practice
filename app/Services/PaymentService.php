<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentService
{
    public function initiatePayment(User $user, $amount, $gateway, $package = null)
    {
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'gateway' => $gateway,
            'status' => 'pending',
            'package' => $package
        ]);

        // Generate payment URL based on gateway
        $paymentUrl = $this->generateGatewayUrl($gateway, $transaction);

        return [
            'transaction' => $transaction,
            'payment_url' => $paymentUrl
        ];
    }

    private function generateGatewayUrl($gateway, $transaction)
    {
        switch ($gateway) {
            case 'bkash':
                return $this->generateBkashUrl($transaction);
            case 'nagad':
                return $this->generateNagadUrl($transaction);
            case 'sslcommerz':
                return $this->generateSslcommerzUrl($transaction);
            default:
                throw new \Exception('Unsupported payment gateway');
        }
    }

    public function handleWebhook($gateway, $payload)
    {
        // Validate and process webhook
        // Update transaction status
        // Award points if successful
    }

    public function initiatePayment(
        User $user,
        float $amount,
        string $gateway,
        array $package = null
    ) {
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'gateway' => $gateway,
            'amount' => $amount,
            'status' => PaymentTransaction::STATUS_PENDING,
            'package' => $package
        ]);

        // Generate payment URL based on gateway
        $paymentUrl = $this->generateGatewayUrl($gateway, $transaction);

        return [
            'transaction' => $transaction,
            'payment_url' => $paymentUrl
        ];
    }

    private function generateGatewayUrl($gateway, $transaction)
    {
        // In a real implementation, this would call the gateway API
        return route('payment.redirect', [
            'gateway' => $gateway,
            'transaction' => $transaction->id
        ]);
    }

    public function handleWebhook($gateway, $payload)
    {
        // Validate gateway signature
        if (!$this->validateGatewaySignature($gateway, $payload)) {
            throw new \Exception('Invalid signature');
        }

        $transactionId = $this->getTransactionIdFromPayload($gateway, $payload);
        $transaction = PaymentTransaction::findOrFail($transactionId);

        if ($this->isPaymentSuccessful($gateway, $payload)) {
            app(PointService::class)->handlePaymentSuccess($transaction);
        } else {
            $transaction->update([
                'status' => PaymentTransaction::STATUS_FAILED,
                'response' => $payload
            ]);
        }
    }
}
