<?php

namespace App\Mail;

use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Log;

class VerifyEmailMail extends Mailable
{
    public $user;
    public $otp;
    public $token;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $otp, string $token, string $type)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        Log::info("Type: " . $this->type);
        if($this->type == VerificationOtp::getTypeCode(VerificationOtp::EMAIL_VERIFICATION)) {
            $subject = 'Verify Email Mail';
        } else if($this->type == VerificationOtp::getTypeCode(VerificationOtp::PASSWORD_RESET)){
            $subject = 'Password Reset Request';
        }
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.verify-email',
        );

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
