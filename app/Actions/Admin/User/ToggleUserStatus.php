<?php

namespace App\Actions\Admin\User;

use App\Enums\Admin\User\UserStatus;
use App\Models\User;

class ToggleUserStatus
{
    public function execute(User $user): User
    {
        $user->update([
            'status' => $user->status === UserStatus::ACTIVE ? UserStatus::PENDING : UserStatus::ACTIVE,
        ]);

        return $user->refresh();
    }
}
