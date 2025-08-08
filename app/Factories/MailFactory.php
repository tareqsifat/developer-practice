<?php
namespace App\Factories;

use App\Models\User;
use App\Models\VerificationOtp;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class MailFactory
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function send(VerificationOtp $verification_otp, User $user): void
    {
        $type = $verification_otp->type;
        switch ($type) {
            case VerificationOtp::EMAIL_VERIFICATION:
                $this->mailService->sendVerificationEmail($user);
                Log::info("Verification Email Sent");
                break;

            case VerificationOtp::PASSWORD_RESET:
                $this->mailService->sendPasswordResetEmail($user);
                Log::info("password_reset Email Sent");
                break;

            default:
                throw new \InvalidArgumentException("Unknown email type: {$type}");
        }
    }
}
