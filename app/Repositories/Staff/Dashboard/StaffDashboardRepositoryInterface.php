<?php

namespace App\Repositories\Staff\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface StaffDashboardRepositoryInterface
{
    public function getTodayAppointments(Carbon $today): Collection;

    public function getTodayAppointmentsCount(Carbon $today): int;

    public function getConfirmedAppointmentsCount(Carbon $today): int;

    public function getPendingAppointmentsCount(Carbon $today): int;

    public function getActiveUsersCount(): int;

    public function getUpcomingAppointments(Carbon $today): Collection;

    public function getAvailableTherapists(): Collection;

    public function getCalendarEvents(): Collection;

    public function getPosts(): Collection;

}
