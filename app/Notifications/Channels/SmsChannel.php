<?php

namespace App\Notifications\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'sendConfirmationSms')) {
            return;
        }

        $notification->sendConfirmationSms($notifiable);
    }
}
