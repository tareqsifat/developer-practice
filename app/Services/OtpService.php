<?php
namespace App\Services;

use App\Models\VerificationOtp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class OtpService
{

    public function generateOtp(Model $verifiable, string $type, int $minutes = 10)
    {
        $numericType = VerificationOtp::getTypeCode($type);
        $verifiable->verificationOtps()
            ->where('verification_type', $numericType)
            ->delete();

        return $verifiable->verificationOtps()->create([
            'otp' => rand(100000, 999999), // 6-digit OTP
            'verification_type' => $numericType,
            'expires_at' => Carbon::now()->addMinutes($minutes)->toDateTimeString(),
            'token' => bin2hex(random_bytes(20)), // 40 hex chars (~39+ chars)
        ]);
    }

    public function verifyOtp(string $otp, string $type, string $token): array
    {
        if(empty($otp) && empty($token)){
            return [
                'success' => false,
                'message' => 'OTP or Token is required',
            ];
        }
        $otpRecord = VerificationOtp::where(function ($query) use ($otp, $token) {
            $query->where('otp', $otp)
                ->orWhere('token', $token);
            })
            ->where('verification_type', $type)
            ->first();

        if (!$otpRecord || $otpRecord->expires_at < now()) {
            return [
                'success' => false,
                'message' => 'OTP is invalid or expired.',
            ];
        }


        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
            'otp' => $otpRecord,
        ];
    }
}
