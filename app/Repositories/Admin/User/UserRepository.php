<?php

namespace App\Repositories\Admin\User;

use App\Enums\Admin\User\UserRole;
use App\Enums\Admin\User\UserStatus;
use App\Models\User;
use App\Repositories\Admin\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUsersPageData(array $filters): array
    {
        $query = User::query()->where('role', UserRole::USER);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['joined_date'])) {
            $query->whereDate('created_at', $filters['joined_date']);
        }

        $users = $query->orderByDesc('is_online')->orderByDesc('last_seen_at')->paginate(5)->withQueryString();

        $stats = [
            'total' => User::where('role', UserRole::USER)->count(),

            'active' => User::where('role', UserRole::USER)->where('status', UserStatus::ACTIVE)->count(),

            'pending' => User::where('role', UserRole::USER)->where('status', UserStatus::PENDING)->count(),

            'online' => User::where('role', UserRole::USER)->where('is_online', true)->count(),
        ];

        return compact('users', 'stats');
    }
}
