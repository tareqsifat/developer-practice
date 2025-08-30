<?php
namespace App\Factories;

use App\Models\User;
use App\Models\VerificationOtp;
use App\Services\MailService;
use Illuminate\Support\Facades\Log;
use stdClass;

class MailFactory
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function send(User $user, VerificationOtp $verification_otp, $type): void
    {

        switch ($type) {
            case VerificationOtp::EMAIL_VERIFICATION:
                Log::info("Sending Verification Email");
                Log::info($verification_otp);
                $this->mailService->sendVerificationEmail($user, $verification_otp);
                Log::info("Verification Email Sent");
                break;

            case VerificationOtp::PASSWORD_RESET:
                $this->mailService->sendPasswordResetEmail($user, $verification_otp);
                Log::info("password_reset Email Sent");
                break;

            default:
                Log::info("Unknown email type: {$type}");
                // throw new \InvalidArgumentException("Unknown email type: {$type}");
        }
    }
}
