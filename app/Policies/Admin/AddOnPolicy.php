<?php

namespace App\Policies\Admin;

use App\Models\AddOns;
use App\Models\User;

class AddOnPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, AddOns $addOn): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, AddOns $addOn): bool
    {
        return $user->isAdmin();
    }
}
