<?php

namespace App\Enums\Admin\User;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case STAFF = 'staff';
}