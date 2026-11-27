<?php

namespace App\Notifications;

use App\Models\WebsiteSetting;
use App\Services\SmsService;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    /**
     * Notification channels.
     */
    public function via($notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    /**
     * Email verification notification.
     */
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

    /**
     * SMS verification notification.
     */
    public function sendConfirmationSms(object $notifiable): void
    {
        if (!config('services.sms.enabled')) {
            return;
        }

        $phone = $notifiable->phone ?? null;

        if (blank($phone)) {
            Log::warning(
                'Email verification SMS skipped: customer phone is missing.'
            );

            return;
        }

        $websiteSettings = WebsiteSetting::first();

        $websiteName = $websiteSettings?->website_name
            ?? 'Padayon Massage Center';

        $message = "{$websiteName}: Thank you for registering!

Your email verification code is: {$this->code}

Please enter this code to verify your email address.

Thank you for choosing Padayon Massage Center!";

        $sent = app(SmsService::class)->send($phone, $message);

        if (!$sent) {
            Log::warning(
                'Email verification SMS was not accepted by UniSMS.'
            );
        }
    }
}
