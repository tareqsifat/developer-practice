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
    protected function key()
    {
        // Prefer a dedicated key: config('services.verify_key') or fallback to app key
        return config('services.verify_key') ?? config('app.key');
    }

    // Base64 URL-safe encode
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Base64 URL-safe decode
    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Generate a token from user id and otp
     *
     * @param  int|string  $userId
     * @param  string      $otp
     * @param  int         $ttlSeconds  (default 3600 = 1 hour)
     * @return string      token (payload.signature)
     */
    public function generateToken($userId, string $otp, int $ttlSeconds = 120): string
    {
        $payload = json_encode([
            'uid' => $userId,
            'otp' => $otp,
            'exp' => time() + $ttlSeconds,
        ]);

        $payloadB64 = $this->base64UrlEncode($payload);

        // raw binary HMAC (true) then base64url encode
        $signatureRaw = hash_hmac('sha256', $payloadB64, $this->key(), true);
        $signatureB64 = $this->base64UrlEncode($signatureRaw);


        return "{$payloadB64}.{$signatureB64}";
    }

    /**
     * Verify token and return payload array on success, or null on failure.
     *
     * @param  string  $token
     * @return array|null  ['uid'=>..., 'otp'=>..., 'exp'=>...]
     */
    public function verifyToken(string $token): ?array
    {
        if (! str_contains($token, '.')) {
            return null;
        }

        [$payloadB64, $signatureB64] = explode('.', $token, 2);

        $expectedSigRaw = hash_hmac('sha256', $payloadB64, $this->key(), true);
        $expectedSigB64 = $this->base64UrlEncode($expectedSigRaw);

        // Constant-time compare
        if (! hash_equals($expectedSigB64, $signatureB64)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        if ($payloadJson === false) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (! is_array($payload) || ! isset($payload['exp'])) {
            return null;
        }

        // Check expiry
        if (time() > intval($payload['exp'])) {
            return null;
        }

        // At this point payload is valid and intact
        return $payload;
    }
}
