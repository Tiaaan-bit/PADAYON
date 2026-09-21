<?php

namespace App\Policies\Admin;

use App\Models\Services;
use App\Models\User;

class ServicePolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Services $service): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Services $service): bool
    {
        return $user->isAdmin();
    }
}
