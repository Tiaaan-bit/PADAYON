<?php

namespace App\Policies\Admin;

use App\Models\User;

class UserPolicy
{
    public function toggleStatus(User $admin, User $user): bool
    {
        return $admin->isAdmin() && !$user->isAdmin();
    }

    public function delete(User $admin, User $user): bool
    {
        return $admin->isAdmin() && !$user->isAdmin();
    }
}
