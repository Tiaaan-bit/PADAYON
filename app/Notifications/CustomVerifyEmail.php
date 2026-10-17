<?php

namespace App\Notifications;

use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $websiteSettings = WebsiteSetting::first();

        return (new MailMessage)
            ->subject('Your Email Verification Code')
            ->view('emails.auth.verify-code', [
                'code' => $this->code,
                'notifiable' => $notifiable,
                'websiteSettings' => $websiteSettings,
            ]);
    }
}
