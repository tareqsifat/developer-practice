<?php
namespace App\Services;

use App\Models\User;
use App\Models\VerificationOtp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Symfony\Component\VarDumper\VarDumper;

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

    public function verifyOtp(string $type, $token = null, $otp = null ): array
    {
        if(empty($otp) && empty($token)){
            return [
                'success' => false,
                'message' => 'OTP or Token is required',
            ];
        }
        Log::info('In OTP servicw: token: ' . $token . ' otp: ' . $otp . ' type: ' . $type);
        $user = User::find(auth()->id());
        $otpRecord = $user->verificationOtps()->where(function ($query) use ($otp, $token) {
            $query->where('otp', $otp)
                ->orWhere('token', $token);
            })
            ->where('verification_type', $type)
            ->first();

        $is_passed = (string) $otpRecord->expires_at < now();

        if (empty($otpRecord) || $otpRecord->expires_at < now()) {
            Log::info('expires_at: '. $otpRecord->expires_at . ' Now: '. now() . ' is time passed ' . $is_passed);
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
