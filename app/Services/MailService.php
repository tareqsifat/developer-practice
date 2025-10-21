<?php
namespace App\Services;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Support\Facades\Mail;
use stdClass;

class MailService
{
    public function sendVerificationEmail(User $user, VerificationOtp $verification_otp, int $type): void
    {
        $otp = $verification_otp->otp;
        $token = $verification_otp->token;
        Mail::to($user->email)->send(new VerifyEmailMail($user,$otp, $token, $type));
    }

    public function sendPasswordResetEmail(User $user, VerificationOtp $verification_otp, int $type): void
    {
        $otp = $verification_otp->otp;
        $token = $verification_otp->token;

        Mail::to($user->email)->send(new VerifyEmailMail($user, $otp, $token, $type));
    }
}
