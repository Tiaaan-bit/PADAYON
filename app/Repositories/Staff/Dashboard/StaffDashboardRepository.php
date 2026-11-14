<?php

namespace App\Repositories\Staff\Dashboard;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffDashboardRepository implements StaffDashboardRepositoryInterface
{
    /**
     * Appointments scheduled for the selected day.
     */
    public function getTodayAppointments(Carbon $today): Collection
    {
        return UsersAppointments::query()
            ->with(['user', 'walkInCustomer', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', $today->toDateString())
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Total appointments scheduled for today.
     */
    public function getTodayAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::query()->whereDate('appointment_date', $today->toDateString())->count();
    }

    /**
     * Pending appointments scheduled for today.
     */
    public function getPendingAppointmentsCount(Carbon $today): int
    {
        return UsersAppointments::query()->whereDate('appointment_date', $today->toDateString())->where('status', AppointmentStatus::PENDING->value)->count();
    }


    /**
     * Appointment status counts for today.
     */
    public function getAppointmentStatistics(Carbon $today): Collection
    {
        return UsersAppointments::query()->select('status')->selectRaw('COUNT(*) as total')->whereDate('appointment_date', $today->toDateString())->groupBy('status')->get();
    }

    /**
     * Retrieve therapists for the status panel.
     */
    public function getTherapists(): Collection
    {
        return Therapists::query()
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find therapists who have a confirmed appointment in progress now.
     *
     * The appointment status stored in the database is "confirm".
     */
    public function getBusyTherapistIds(Carbon $now): Collection
    {
        return UsersAppointments::query()->whereNotNull('therapist_id')->whereDate('appointment_date', $now->toDateString())->where('status', AppointmentStatus::CONFIRMED->value)->where('appointment_time', '<=', $now->format('H:i:s'))->where('appointment_end_time', '>', $now->format('H:i:s'))->pluck('therapist_id')->unique()->values();
    }

    /**
     * Appointments requiring the admin's attention.
     */
    public function getNeedsAttention(Carbon $today): Collection
    {
        return UsersAppointments::query()
            ->with(['user', 'walkInCustomer', 'service', 'therapist'])
            ->whereDate('appointment_date', $today->toDateString())
            ->where(function ($query) {
                $query->where('status', AppointmentStatus::PENDING->value)->orWhereIn('payment_status', ['pending', 'failed']);
            })
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }



    /**
     * Recent activity represented by recently created appointments.
     *
     * This avoids assuming that a separate activity-log table exists.
     */
    public function getRecentActivity(): Collection
    {
        return UsersAppointments::query()
            ->with(['user', 'walkInCustomer', 'service', 'therapist'])
            ->latest('created_at')
            ->limit(6)
            ->get();
    }
}
