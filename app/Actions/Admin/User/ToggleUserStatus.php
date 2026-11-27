<?php

namespace App\Actions\Admin\User;

use App\Enums\Admin\User\UserStatus;
use App\Models\User;

class ToggleUserStatus
{
    public function execute(User $user): User
    {
        $isDeactivating = $user->status === UserStatus::ACTIVE;

        $user->update([
            'status' => $isDeactivating
                ? UserStatus::PENDING
                : UserStatus::ACTIVE,

            'email_verified_at' => $isDeactivating
                ? null
                : $user->email_verified_at,

            'requires_admin_reactivation' => $isDeactivating,
        ]);

        return $user->refresh();
    }
}
