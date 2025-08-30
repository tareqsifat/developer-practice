<?php

namespace App\Events;

use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $user;
    public $verification_otp;
    public $type;

    public function __construct(User $user, VerificationOtp $verification_otp, $type)
    {
        $this->user = $user;
        $this->verification_otp = $verification_otp;
        $this->type = $type;
    }
}
