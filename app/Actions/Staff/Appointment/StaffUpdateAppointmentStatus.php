<?php

namespace App\Actions\Staff\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Models\UsersAppointments;
use App\Repositories\Staff\Appointment\StaffAppointmentRepositoryInterface;
use Illuminate\Validation\ValidationException;

class StaffUpdateAppointmentStatus
{
    public function __construct(protected StaffAppointmentRepositoryInterface $appointmentRepository) {}

    public function execute(UsersAppointments $appointment, AppointmentStatus $newStatus): UsersAppointments
    {
        // Only pending appointments paid at the branch may be updated.
        if ($appointment->status !== AppointmentStatus::PENDING || $appointment->payment_method !== 'branch') {
            throw ValidationException::withMessages([
                'status' => 'Only pending appointments paid at the branch may be updated.',
            ]);
        }

        // Only these transitions are allowed through this action.
        $allowedStatuses = [AppointmentStatus::CONFIRMED, AppointmentStatus::REJECTED, AppointmentStatus::CANCELLED, AppointmentStatus::NO_SHOW];

        if (!in_array($newStatus, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'The selected appointment status is not allowed.',
            ]);
        }

        $oldStatus = $appointment->status;

        $appointment = $this->appointmentRepository->updateStatus($appointment, $newStatus);

        // Set payment status to unpaid for these appointment outcomes.
        if (in_array($newStatus, [AppointmentStatus::REJECTED, AppointmentStatus::CANCELLED, AppointmentStatus::NO_SHOW], true)) {
            $appointment->update([
                'payment_status' => 'unpaid',
            ]);
        }

        $appointment->refresh();

        AppointmentStatusChanged::dispatch($appointment, $oldStatus, $newStatus);

        return $appointment;
    }
}
