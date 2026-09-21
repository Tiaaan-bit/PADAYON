<?php

namespace App\Listeners\Admin\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Events\Admin\Appointment\AppointmentStatusChanged;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentNoShowNotification;
use App\Notifications\AppointmentRejectedNotification;

class SendAppointmentStatusNotification
{
    public function handle(AppointmentStatusChanged $event): void
    {
        $appointment = $event->appointment;
        $user = $appointment->user;

        if (!$user) {
            return;
        }

        switch ($event->newStatus) {

            case AppointmentStatus::CONFIRMED:

                $user->notify(
                    new AppointmentConfirmedNotification($appointment)
                );

                break;

            case AppointmentStatus::REJECTED:

                $user->notify(
                    new AppointmentRejectedNotification($appointment)
                );

                break;

            case AppointmentStatus::CANCELLED:

                $user->notify(
                    new AppointmentCancelledNotification($appointment)
                );

                break;

            case AppointmentStatus::NO_SHOW:

                $user->notify(
                    new AppointmentNoShowNotification($appointment)
                );

                break;
        }
    }
}

