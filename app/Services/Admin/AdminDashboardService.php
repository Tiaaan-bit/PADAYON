<?php

namespace App\Services\Admin;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Repositories\Admin\Dashboard\DashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    private string $timezone = 'Asia/Manila';

    public function __construct(protected DashboardRepositoryInterface $dashboardRepository) {}

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
                /*
                |--------------------------------------------------------------------------
                | Appointment Date
                |--------------------------------------------------------------------------
                |
                | appointment_date is the REAL calendar date.
                |
                | Do NOT move appointments before 1 PM to another day.
                |
                */

                $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

                /*
                |--------------------------------------------------------------------------
                | Appointment Start
                |--------------------------------------------------------------------------
                */

                $start = null;

                if ($appointment->appointment_time) {
                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . Carbon::parse($appointment->appointment_time, $this->timezone)->format('H:i:s'), $this->timezone);
                }

                /*
                |--------------------------------------------------------------------------
                | Appointment End
                |--------------------------------------------------------------------------
                */

                $end = null;

                if ($appointment->appointment_end_time && $start) {
                    $end = Carbon::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . Carbon::parse($appointment->appointment_end_time, $this->timezone)->format('H:i:s'), $this->timezone);

                    /*
                    |--------------------------------------------------------------------------
                    | Cross-Midnight Appointment
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    |
                    | Sept 30
                    | 11:30 PM -> 12:30 AM
                    |
                    | End becomes Oct 1.
                    |
                    */

                    if ($end->lessThanOrEqualTo($start)) {
                        $end->addDay();
                    }
                }

                return [
                    'id' => $appointment->id,

                    /*
                    |--------------------------------------------------------------------------
                    | Calendar Date
                    |--------------------------------------------------------------------------
                    */

                    'date' => $start ? $start->format('Y-m-d') : $appointmentDate,

                    /*
                    |--------------------------------------------------------------------------
                    | Start Time
                    |--------------------------------------------------------------------------
                    */

                    'time' => $start ? $start->format('h:i A') : 'N/A',

                    /*
                    |--------------------------------------------------------------------------
                    | End Time
                    |--------------------------------------------------------------------------
                    */

                    'end_time' => $end ? $end->format('h:i A') : null,

                    /*
                    |--------------------------------------------------------------------------
                    | Service
                    |--------------------------------------------------------------------------
                    */

                    'title' => $appointment->service?->name ?? 'Service',

                    /*
                    |--------------------------------------------------------------------------
                    | User
                    |--------------------------------------------------------------------------
                    */

                    'user' => $appointment->user?->name ?? 'N/A',

                    /*
                    |--------------------------------------------------------------------------
                    | Therapist
                    |--------------------------------------------------------------------------
                    */

                    'therapist' => $appointment->therapist?->name ?? 'N/A',

                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */

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
