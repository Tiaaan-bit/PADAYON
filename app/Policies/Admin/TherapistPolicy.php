<?php

namespace App\Policies\Admin;

use App\Models\User;
use App\Models\Therapists;

class TherapistPolicy
{
    public function update(User $user, Therapists $therapist): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Therapists $therapist): bool
    {
        return $user->isAdmin();
    }
}