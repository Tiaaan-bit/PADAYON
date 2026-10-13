<?php

namespace App\Policies\Admin;

use App\Models\ImagePost;
use App\Models\User;

class ImagePostPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ImagePost $imagePost): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ImagePost $imagePost): bool
    {
        return $user->isAdmin();
    }
}