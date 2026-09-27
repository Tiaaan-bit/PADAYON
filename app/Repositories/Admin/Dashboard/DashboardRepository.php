<?php

namespace App\Repositories\Admin\Dashboard;

use App\Models\Post;
use App\Models\Therapists;
use App\Models\User;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardRepository implements DashboardRepositoryInterface
{

    private string $timezone = 'Asia/Manila';


    public function getTodayAppointments(Carbon $today): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', $today->toDateString())
            ->orderBy('appointment_time')
            ->get();
    }


    public function getTodayAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::query()->whereDate('appointment_date', $today->toDateString())->count();
    }


    public function getConfirmedAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::query()->whereDate('appointment_date', $today->toDateString())->where('status', 'confirm')->count();
    }


    public function getPendingAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::query()->whereDate('appointment_date', $today->toDateString())->where('status', 'pending')->count();
    }

    public function getActiveUsersCount(): int
    {
        return User::query()->where('role', 'user')->where('status', 'active')->count();
    }


    public function getUpcomingAppointments(Carbon $today): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', '>', $today->toDateString())
            ->whereNotIn('status', ['cancelled', 'rejected', 'failed', 'no show'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get available therapists.
     */
    public function getAvailableTherapists(): Collection
    {
        return Therapists::query()->where('status', 'available')->orderBy('name')->get();
    }

    public function getCalendarEvents(): Collection
    {
        return UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereIn('status', ['pending', 'confirm'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get published posts.
     */
    public function getPosts(): Collection
    {
        return Post::with('author')->whereNotNull('published_at')->latest('published_at')->get();
    }
}
