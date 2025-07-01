<?php

// app/Http/Controllers/Api/PaymentWebhookController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, $gateway, PaymentService $paymentService)
    {
        try {
            $paymentService->handleWebhook($gateway, $request->all());
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

