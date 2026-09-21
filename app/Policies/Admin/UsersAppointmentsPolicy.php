<?php

namespace App\Policies\Admin;

use App\Models\User;
use App\Models\UsersAppointments;

class UsersAppointmentsPolicy
{
    public function updateStatus(User $user, UsersAppointments $appointment): bool
    {
        return $user->isAdmin();
    }

    public function cancel(User $user, UsersAppointments $appointment): bool
    {
        return $user->isAdmin();
    }
}
