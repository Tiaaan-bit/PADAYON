<?php

namespace App\Actions\Admin\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Models\UsersAppointments;
use App\Repositories\Admin\Appointment\AppointmentRepositoryInterface;

class UpdateAppointmentStatus
{
    public function __construct(protected AppointmentRepositoryInterface $appointmentRepository) {}

    public function execute(UsersAppointments $appointment, AppointmentStatus $newStatus): UsersAppointments
    {
        $oldStatus = $appointment->status;

        if ($oldStatus === $newStatus) {
            return $appointment;
        }

        $appointment = $this->appointmentRepository->updateStatus($appointment, $newStatus);

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
