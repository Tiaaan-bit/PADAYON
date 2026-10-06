<?php

namespace App\Services\Staff;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Repositories\Staff\Dashboard\StaffDashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffDashboardService
{
    private string $timezone = 'Asia/Manila';

    public function __construct(protected StaffDashboardRepositoryInterface $dashboardRepository) {}

    public function getDashboardData(): array
    {
        $today = Carbon::today($this->timezone);

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
                $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

                $start = null;

                if ($appointment->appointment_time) {
                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . Carbon::parse($appointment->appointment_time, $this->timezone)->format('H:i:s'), $this->timezone);
                }

                $end = null;

                if ($appointment->appointment_end_time && $start) {
                    $end = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . Carbon::parse($appointment->appointment_end_time, $this->timezone)->format('H:i:s'), $this->timezone);

                    if ($end->lessThanOrEqualTo($start)) {
                        $end->addDay();
                    }
                }

                return [
                    'id' => $appointment->id,

                    'date' => $start ? $start->format('Y-m-d') : $appointmentDate,

                    'time' => $start ? $start->format('h:i A') : 'N/A',

                    'end_time' => $end ? $end->format('h:i A') : null,

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
                        default => 'Unknown',
                    },
                ];
            })
            ->values()
            ->all();
    }
}
