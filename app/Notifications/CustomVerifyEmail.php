<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Verification Code')
            ->greeting('Hello!')
            ->line('Your email verification code is:')
            ->line($this->code)
            ->line('This code will expire in 5 minutes.')
            ->line('If you did not request this, please ignore this email.');
    }
}