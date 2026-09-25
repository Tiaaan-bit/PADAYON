<?php

namespace App\Enums\Admin\Appointment;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirm';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no show';
    case FAILED = 'failed';


    public function label(): string
    {
        return match($this){
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
            self::FAILED => 'Failed',

            
        };
    }

}


