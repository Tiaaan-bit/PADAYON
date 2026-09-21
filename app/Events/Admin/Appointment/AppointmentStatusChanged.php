<?php

namespace App\Events\Admin\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\UsersAppointments;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public UsersAppointments $appointment, public AppointmentStatus $oldStatus, public AppointmentStatus $newStatus) {}
}
