<?php

namespace App\Services\Admin;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Repositories\Admin\Dashboard\DashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    public function __construct(protected DashboardRepositoryInterface $dashboardRepository) {}

    public function getDashboardData(): array
    {
        $today = Carbon::today('Asia/Manila');

        return [
            'appointments' => $this->dashboardRepository->getTodayAppointments($today),

            'todayAppointmentsCount' => $this->dashboardRepository->getTodayAppointmentsCount($today),

            'confirmedAppointmentsCount' => $this->dashboardRepository->getConfirmedAppointmentsCount($today),

            'pendingAppointmentsCount' => $this->dashboardRepository->getPendingAppointmentsCount($today),

            'activeUsersCount' => $this->dashboardRepository->getActiveUsersCount(),

            'upcomingAppointments' => $this->dashboardRepository->getUpcomingAppointments($today),

            'therapists' => $this->dashboardRepository->getAvailableTherapists(),

            'calendarEvents' => $this->formatCalendarEvents($this->dashboardRepository->getCalendarEvents()),

            'posts' => $this->dashboardRepository->getPosts(),
        ];
    }

    protected function formatCalendarEvents(Collection $appointments): array
    {
        return $appointments
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,

                    'date' => Carbon::parse($appointment->appointment_date, 'Asia/Manila')->format('Y-m-d'),

                    'time' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time, 'Asia/Manila')->format('h:i A') : 'N/A',

                    'title' => $appointment->service?->name ?? 'Service',

                    'user' => $appointment->user?->name ?? 'N/A',

                    'therapist' => $appointment->therapist?->name ?? 'N/A',

                    'status' => match ($appointment->status) {
                        AppointmentStatus::CONFIRMED => 'Confirmed',
                        AppointmentStatus::PENDING => 'Pending',
                        AppointmentStatus::REJECTED => 'Rejected',
                        AppointmentStatus::CANCELLED => 'Cancelled',
                        AppointmentStatus::NO_SHOW => 'No Show',
                        AppointmentStatus::FAILED => 'Failed',
                    },
                ];
            })
            ->values()
            ->all();
    }
}
