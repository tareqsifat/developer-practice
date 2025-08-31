<?php
namespace App\Services;

use App\Models\VerificationOtp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function generateOtp(Model $verifiable, string $type, int $minutes = 10): VerificationOtp
    {
        $numericType = (new VerificationOtp)->getVerificationTypeAttribute($type);
        $verifiable->verificationOtps()
            ->where('verification_type', $numericType)
            ->delete();

        return $verifiable->verificationOtps()->create([
            'otp' => rand(100000, 999999), // 6-digit OTP
            'verification_type' => $type,
            'expires_at' => Carbon::now()->addMinutes($minutes)->toDateTimeString(),
            'token' => bin2hex(random_bytes(20)), // 40 hex chars (~39+ chars)
        ]);
    }

    public function verifyOtp(Model $verifiable, string $otp, string $type): bool
    {
        $otpRecord = $verifiable->verificationOtps()
            ->where('otp', $otp)
            ->where('verification_type', $type)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            $otpRecord->delete();
            return true;
        }

        return false;
    }
}
