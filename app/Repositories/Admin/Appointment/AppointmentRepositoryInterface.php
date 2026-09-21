<?php

namespace App\Repositories\Admin\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\UsersAppointments;

interface AppointmentRepositoryInterface
{
    public function getAppointmentPageData(array $filters): array;

    public function updateStatus(UsersAppointments $appointment, AppointmentStatus $status): UsersAppointments;

    public function cancel(UsersAppointments $appointment): UsersAppointments;
}
