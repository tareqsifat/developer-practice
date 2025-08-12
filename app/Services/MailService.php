<?php
namespace App\Services;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailService
{
    public function sendVerificationEmail(User $user): void
    {
        $token = Str::random(64);

        $user->update([
            'email_verification_token' => $token,
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user, $token));
    }

    public function sendPasswordResetEmail(User $user): void
    {
        $token = Str::random(64);

        $user->update([
            'password_reset_token' => $token,
        ]);

        Mail::send('emails.password-reset', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset Request');
        });
    }
}
