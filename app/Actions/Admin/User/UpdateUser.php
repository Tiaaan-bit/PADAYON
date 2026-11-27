<?php

namespace App\Actions\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUser
{
    public function execute(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $emailChanged = $user->email !== $data['email'];

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($emailChanged) {
                $userData['email_verified_at'] = null;

                // Clear your existing email verification fields
                $userData['email_verification_code'] = null;
                $userData['email_verification_code_expires_at'] = null;
            }

            $user->update($userData);

            return $user->refresh();
        });
    }
}
