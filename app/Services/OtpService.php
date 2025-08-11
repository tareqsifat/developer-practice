<?php
namespace App\Services;

use App\Models\VerificationOtp;
use Illuminate\Database\Eloquent\Model;
class OtpService
{
    public function generateOtp(Model $verifiable, string $type, int $minutes = 10): VerificationOtp
    {
        // Delete existing OTPs for this verifiable
        $verifiable->verificationOtps()
            ->where('verification_type', $type)->first();
        if(!empty($verifiable)){
            $verifiable->delete();
        }

        return $verifiable->verificationOtps()->create([
            'otp' => rand(100000, 999999), // 6-digit OTP
            'verification_type' => $type,
            'expires_at' => now()->addMinutes($minutes)
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
