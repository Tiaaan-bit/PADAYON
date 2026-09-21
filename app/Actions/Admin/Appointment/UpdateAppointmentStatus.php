<?php

namespace App\Actions\Admin\Appointment;

use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Models\UsersAppointments;
use App\Repositories\Admin\Appointment\AppointmentRepositoryInterface;
use App\Enums\Admin\Appointment\AppointmentStatus;


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

        AppointmentStatusChanged::dispatch($appointment, $oldStatus, $newStatus);

        return $appointment;
    }
}
