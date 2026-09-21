<?php

namespace App\Enums\Admin\User;

enum UserStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
}