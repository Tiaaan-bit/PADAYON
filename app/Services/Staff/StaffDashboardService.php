<?php

namespace App\Services\Staff;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Repositories\Staff\Dashboard\StaffDashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffDashboardService
{
    public function __construct(protected StaffDashboardRepositoryInterface $dashboardRepository) {}

    public function getDashboardData(): array
    {
        $now = Carbon::now('Asia/Manila');
        
        $today = $now->copy()->startOfDay();

        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        $appointments = $this->dashboardRepository->getTodayAppointments($today);

        $therapists = $this->prepareTherapistStatuses($this->dashboardRepository->getTherapists(), $this->dashboardRepository->getBusyTherapistIds($now));

        $appointmentStatistics = $this->prepareAppointmentStatistics($this->dashboardRepository->getAppointmentStatistics($today));

        return [
            'now' => $now,
            
            'todayAppointments' => $appointments,

            'todayAppointmentsCount' => $this->dashboardRepository->getTodayAppointmentsCount($today),

            'pendingAppointmentsCount' => $this->dashboardRepository->getPendingAppointmentsCount($today),

            'therapists' => $therapists,

            'availableTherapistsCount' => $therapists->where('dashboard_status', 'available')->count(),

            'busyTherapistsCount' => $therapists->where('dashboard_status', 'busy')->count(),

            'offlineTherapistsCount' => $therapists->where('dashboard_status', 'offline')->count(),

            'needsAttention' => $this->dashboardRepository->getNeedsAttention($today),

            'appointmentStatistics' => $appointmentStatistics,

            'popularServices' => $this->dashboardRepository->getPopularServices($today),

            'recentActivity' => $this->dashboardRepository->getRecentActivity(),
        ];
    }

    /**
     * Determine whether each therapist is available, busy, or offline.
     *
     * The database status is still the source of availability.
     * "Busy" is calculated from a confirmed appointment in progress.
     */
    protected function prepareTherapistStatuses(Collection $therapists, Collection $busyTherapistIds): Collection
    {
        $busyIds = $busyTherapistIds->map(fn($id) => (int) $id)->all();

        return $therapists->map(function ($therapist) use ($busyIds) {
            if (in_array((int) $therapist->id, $busyIds, true)) {
                $dashboardStatus = 'busy';
            } elseif ($therapist->status === 'available') {
                $dashboardStatus = 'available';
            } else {
                $dashboardStatus = 'offline';
            }

            $therapist->setAttribute('dashboard_status', $dashboardStatus);

            return $therapist;
        });
    }

    /**
     * Build a complete Monday-to-Sunday series, including days with
     * zero revenue.
     */
    protected function prepareWeeklyRevenue(Collection $revenueRows, Carbon $startOfWeek): Collection
    {
        $revenueByDate = $revenueRows->keyBy('revenue_date');

        $days = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateKey = $date->toDateString();

            $row = $revenueByDate->get($dateKey);

            $days->push([
                'date' => $dateKey,
                'label' => $date->format('D'),
                'amount' => (float) ($row->total_revenue ?? 0),
            ]);
        }

        return $days;
    }

    /**
     * Present status counts in a consistent order.
     */
    protected function prepareAppointmentStatistics(Collection $statistics): Collection
    {
        $counts = $statistics->keyBy(function ($row) {
            return $row->status instanceof AppointmentStatus ? $row->status->value : (string) $row->status;
        });

        return collect([
            [
                'status' => AppointmentStatus::PENDING->value,
                'label' => AppointmentStatus::PENDING->label(),
            ],
            [
                'status' => AppointmentStatus::CONFIRMED->value,
                'label' => AppointmentStatus::CONFIRMED->label(),
            ],
            [
                'status' => AppointmentStatus::REJECTED->value,
                'label' => AppointmentStatus::REJECTED->label(),
            ],
            [
                'status' => AppointmentStatus::CANCELLED->value,
                'label' => AppointmentStatus::CANCELLED->label(),
            ],
            [
                'status' => AppointmentStatus::NO_SHOW->value,
                'label' => AppointmentStatus::NO_SHOW->label(),
            ],
            [
                'status' => AppointmentStatus::FAILED->value,
                'label' => AppointmentStatus::FAILED->label(),
            ],
        ])->map(function (array $item) use ($counts) {
            $row = $counts->get($item['status']);

            $item['count'] = (int) ($row->total ?? 0);

            return $item;
        });
    }
}
