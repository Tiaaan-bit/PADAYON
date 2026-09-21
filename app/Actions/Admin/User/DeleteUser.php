<?php

namespace App\Actions\Admin\User;

use App\Models\User;

class DeleteUser
{
    public function execute(User $user): bool
    {
        return $user->delete();
    }
}
