<?php

namespace App\Notifications;

use App\Models\WebsiteSetting;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.auth.reset-password', [
                'url' => $url,
                'websiteSettings' => WebsiteSetting::first(),
                'expireMinutes' => config('auth.passwords.users.expire', 60),
            ]);
    }
}
