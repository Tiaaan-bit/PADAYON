<?php

namespace App\Repositories\Admin\Dashboard;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
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
     * Actual revenue recorded as received on the selected day.
     *
     * amount_paid = amount received.
     * paid_at = date/time the received amount was recorded.
     *
     * Do not require payment_status = paid because a downpayment
     * can exist while the remaining balance is still outstanding.
     */
    /**
     * Revenue associated with appointments scheduled today.
     */
    public function getTodayRevenue(Carbon $today): float
    {
        return (float) UsersAppointments::query()
            ->whereDate('appointment_date', $today->toDateString())
            ->where('amount_paid', '>', 0)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
            ->sum('amount_paid');
    }

    /**
     * Revenue grouped by scheduled appointment date for the requested week.
     */
    public function getWeeklyRevenue(Carbon $startOfWeek, Carbon $endOfWeek): Collection
    {
        return UsersAppointments::query()
            ->selectRaw('DATE(appointment_date) as revenue_date')
            ->selectRaw('SUM(amount_paid) as total_revenue')
            ->whereBetween('appointment_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('amount_paid', '>', 0)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
            ->groupBy(DB::raw('DATE(appointment_date)'))
            ->orderBy('revenue_date')
            ->get();
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
        return Therapists::query()->orderBy('name')->get();
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
     * Most-booked services based on appointments scheduled today.
     */
    public function getPopularServices(Carbon $today): Collection
    {
        return Services::query()
            ->withCount([
                'appointments as appointments_count' => function ($query) use ($today) {
                    $query->whereDate('appointment_date', $today->toDateString());
                },
            ])
            ->orderByDesc('appointments_count')
            ->limit(5)
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
