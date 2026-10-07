<?php

namespace App\Actions\Staff\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Models\UsersAppointments;
use App\Repositories\Staff\Appointment\StaffAppointmentRepositoryInterface;

class StaffCancelAppointment
{
    public function __construct(
        protected StaffAppointmentRepositoryInterface $appointmentRepository
    ) {}

    public function execute(UsersAppointments $appointment): UsersAppointments
    {
        $oldStatus = $appointment->status;

        if ($oldStatus === AppointmentStatus::CANCELLED) {
            return $appointment;
        }

        $appointment = $this->appointmentRepository->cancel($appointment);

    
        $appointment->update([
            'payment_status' => 'unpaid',
        ]);


        $appointment->refresh();


        AppointmentStatusChanged::dispatch(
            $appointment,
            $oldStatus,
            AppointmentStatus::CANCELLED
        );

        return $appointment;
    }
}

