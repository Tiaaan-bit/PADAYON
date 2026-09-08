<?php

namespace App\Services\Auth;

use App\Models\User;

class EmailVerificationService
{
    public function resend(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }

    public function verify(User $user, string $code)
    {
        if (!$user->email_verification_code || !$user->email_verification_code_expires_at || now()->greaterThan($user->email_verification_code_expires_at) || $code !== $user->email_verification_code) {
            return false;
        }

        $user
            ->forceFill([
                'email_verified_at' => now(),
                'status' => 'active',
                'email_verification_code' => null,
                'email_verification_code_expires_at' => null,
            ])
            ->save();

        return true;
    }
}
