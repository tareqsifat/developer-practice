<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Factories\MailFactory;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $this->mailFactory->send($event->verification_otp, $event->user);
    }
}
