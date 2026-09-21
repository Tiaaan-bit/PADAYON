<?php

namespace App\Repositories\Admin\Dashboard;

use App\Models\User;
use App\Models\UsersAppointments;
use App\Models\Therapists;
use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Support\Collection;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getTodayAppointments(Carbon $today): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();
    }

    public function getTodayAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::whereDate('appointment_date', $today)->count();
    }

    public function getConfirmedAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::whereDate('appointment_date', $today)->where('status', 'confirm')->count();
    }

    public function getPendingAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::whereDate('appointment_date', $today)->where('status', 'pending')->count();
    }

    public function getActiveUsersCount(): int
    {
        return User::where('role', 'user')->where('status', 'active')->count();
    }

    public function getUpcomingAppointments(Carbon $today): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'no show'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    public function getAvailableTherapists(): Collection
    {
        return Therapists::where('status', 'available')->orderBy('name')->get();
    }

    public function getCalendarEvents(): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist'])
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    public function getPosts(): Collection
    {
        return Post::with('author')->whereNotNull('published_at')->latest('published_at')->get();
    }
}
