<?php

namespace App\Support;

final class BookingRules
{
    public const MAX_ACTIVE_UPCOMING_APPOINTMENTS = 3;

    public const MAX_RESCHEDULES_PER_APPOINTMENT = 1;


    private function __construct() {}
}
