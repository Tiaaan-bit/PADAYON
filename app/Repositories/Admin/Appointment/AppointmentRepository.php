<?php

namespace App\Repositories\Admin\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\Services;
use App\Models\UsersAppointments;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getAppointmentPageData(array $filters): array
    {
        $query = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }

        if (!empty($filters['appointment_date'])) {
            $query->whereDate('appointment_date', $filters['appointment_date']);
        }

        return [
            'totalAppointments' => UsersAppointments::count(),

            'totalConfirmed' => UsersAppointments::where('status', 'confirm')->count(),

            'totalRejected' => UsersAppointments::where('status', 'rejected')->count(),

            'totalPending' => UsersAppointments::where('status', 'pending')->count(),

            'totalCancelled' => UsersAppointments::where('status', 'cancelled')->count(),

            'totalNoShow' => UsersAppointments::where('status', 'no show')->count(),

            'appointments' => $query->paginate(5)->withQueryString(),

            'services' => Services::orderBy('name')->get(),
        ];
    }

    public function updateStatus(UsersAppointments $appointment, AppointmentStatus $status): UsersAppointments
    {
        $appointment->update([
            'status' => $status,
        ]);

        return $appointment->refresh();
    }

    public function cancel(UsersAppointments $appointment): UsersAppointments
    {
        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
        ]);

        return $appointment->refresh();
    }
}
