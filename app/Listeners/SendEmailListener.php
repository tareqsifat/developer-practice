<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Factories\MailFactory;
use App\Models\VerificationOtp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendEmailListener implements ShouldQueue
{

    protected $mailFactory;
    /**
     * Create the event listener.
     */
    public function __construct(MailFactory $mailFactory)
    {
        $this->mailFactory = $mailFactory;
    }

    /**
     * Handle the event.
     */
    public function handle(SendEmailEvent $event): void
    {
        $this->mailFactory->send($event->user, $event->verification_otp, $event->type);
    }
}
