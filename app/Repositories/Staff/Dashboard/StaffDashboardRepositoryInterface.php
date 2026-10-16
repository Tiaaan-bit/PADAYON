<?php

namespace App\Repositories\Staff\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface StaffDashboardRepositoryInterface
{
    public function getTodayAppointments(Carbon $today): Collection;

    public function getTodayAppointmentsCount(Carbon $today): int;

    public function getPendingAppointmentsCount(Carbon $today): int;

    public function getAppointmentStatistics(Carbon $today): Collection;

    public function getTherapists(): Collection;

    public function getBusyTherapistIds(Carbon $now): Collection;

    public function getNeedsAttention(Carbon $today): Collection;

    public function getPopularServices(Carbon $today): Collection;

    public function getRecentActivity(): Collection;
}
