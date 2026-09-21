<?php

namespace App\Actions\Admin\Appointment;

use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Models\UsersAppointments;
use App\Repositories\Admin\Appointment\AppointmentRepositoryInterface;
use App\Enums\Admin\Appointment\AppointmentStatus;


class CancelAppointment
{
    public function __construct(protected AppointmentRepositoryInterface $appointmentRepository) {}

    public function execute(UsersAppointments $appointment): UsersAppointments
    {
        $oldStatus = $appointment->status;

        if ($oldStatus === AppointmentStatus::CANCELLED) {
            return $appointment;
        }

        $appointment = $this->appointmentRepository->cancel($appointment);

        AppointmentStatusChanged::dispatch($appointment, $oldStatus, AppointmentStatus::CANCELLED);

        return $appointment;
    }
}
