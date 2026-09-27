<?php

namespace App\Actions\User\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RescheduleAppointment
{
    private string $timezone = 'Asia/Manila';

    private int $slotInterval = 30;

    /**
     * Booking windows for each calendar date:
     *
     * 12:00 AM - 1:00 AM
     * 1:00 PM  - 12:00 AM
     */
    private function getBookingWindows(string $date): array
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        return [
            [
                'opening' => $bookingDate->copy()->setTime(0, 0, 0),
                'closing' => $bookingDate->copy()->setTime(1, 0, 0),
            ],
            [
                'opening' => $bookingDate->copy()->setTime(13, 0, 0),
                'closing' => $bookingDate->copy()->addDay()->setTime(0, 0, 0),
            ],
        ];
    }

    public function execute(UsersAppointments $appointment, string $newDate, string $newTime): UsersAppointments
    {
        $this->validateAppointment($appointment);

        /*
        |--------------------------------------------------------------------------
        | Validate Date
        |--------------------------------------------------------------------------
        */

        $appointmentDate = Carbon::createFromFormat('Y-m-d', $newDate, $this->timezone)->startOfDay();

        $today = $this->manilaNow()->startOfDay();

        if ($appointmentDate->lt($today)) {
            throw ValidationException::withMessages([
                'appointment_date' => 'The new appointment date cannot be in the past.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Build Start DateTime
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | 12:30 AM stays on the selected calendar date.
        |
        | Example:
        |
        | September 30 12:30 AM
        | = September 30 12:30 AM
        |
        */

        $start = $this->appointmentDateTime($newDate, $newTime);

        /*
        |--------------------------------------------------------------------------
        | Validate 30-Minute Interval
        |--------------------------------------------------------------------------
        */

        if ((int) $start->minute !== 0 && (int) $start->minute !== 30) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Appointments can only start on a 30-minute interval.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Booking Window
        |--------------------------------------------------------------------------
        */

        $this->validateStartInsideBookingWindow($start, $newDate);

        /*
        |--------------------------------------------------------------------------
        | Validate Past Time
        |--------------------------------------------------------------------------
        */

        if ($start->lte($this->manilaNow())) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment time has already passed.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Existing Appointment Duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) ($appointment->service_duration_minutes ?? 0);

        $addOnDuration = (int) ($appointment->addons_duration_minutes ?? 0);

        $totalDuration = $serviceDuration + $addOnDuration;

        if ($totalDuration <= 0) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Unable to determine the appointment duration.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate End
        |--------------------------------------------------------------------------
        */

        $end = $start->copy()->addMinutes($totalDuration);

        /*
        |--------------------------------------------------------------------------
        | Appointment Must Stay Inside ONE Booking Window
        |--------------------------------------------------------------------------
        |
        | We do not allow an appointment to cross:
        |
        | 1:00 AM
        |
        | or
        |
        | 12:00 AM
        |
        */

        if (!$this->fitsInsideBookingWindow($start, $end, $newDate)) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment duration does not fit within the available booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Lock Therapist + Final Conflict Check
        |--------------------------------------------------------------------------
        */

        $updatedAppointment = DB::transaction(function () use ($appointment, $newDate, $newTime, $start, $end) {
            $therapist = Therapists::query()->where('id', $appointment->therapist_id)->where('status', 'available')->lockForUpdate()->first();

            if (!$therapist) {
                throw ValidationException::withMessages([
                    'appointment_time' => 'The selected therapist is no longer available.',
                ]);
            }

            /*
                |--------------------------------------------------------------------------
                | Get Appointments From Previous + Selected Calendar Date
                |--------------------------------------------------------------------------
                |
                | Previous date is required because an appointment can cross
                | midnight into the selected date.
                */

            $selectedDate = Carbon::createFromFormat('Y-m-d', $newDate, $this->timezone)->startOfDay();

            $previousDate = $selectedDate->copy()->subDay()->format('Y-m-d');

            $existingAppointments = UsersAppointments::query()
                ->where('therapist_id', $appointment->therapist_id)
                ->whereIn('appointment_date', [$previousDate, $newDate])
                ->where('id', '!=', $appointment->id)
                ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->get();

            /*
                |--------------------------------------------------------------------------
                | Final Conflict Check
                |--------------------------------------------------------------------------
                */

            foreach ($existingAppointments as $existingAppointment) {
                $existingRange = $this->getAppointmentRange($existingAppointment);

                if ($this->appointmentOverlaps($start, $end, $existingRange['start'], $existingRange['end'])) {
                    throw ValidationException::withMessages([
                        'appointment_time' => 'The selected time is no longer available. Please choose another schedule.',
                    ]);
                }
            }

            /*
                |--------------------------------------------------------------------------
                | Update Only Schedule
                |--------------------------------------------------------------------------
                */

            $appointment->update([
                'appointment_date' => $newDate,
                'appointment_time' => $start->format('H:i'),
                'appointment_end_time' => $end->format('H:i'),
            ]);

            return $appointment->fresh(['service', 'therapist', 'addOn']);
        });

        return $updatedAppointment;
    }

    /*
    |--------------------------------------------------------------------------
    | Manila Now
    |--------------------------------------------------------------------------
    */

    private function manilaNow(): Carbon
    {
        return Carbon::now($this->timezone);
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment DateTime
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | There is NO special handling for 12 AM.
    |
    | 12:30 AM on September 30 means September 30 12:30 AM.
    |
    */

    private function appointmentDateTime(string $date, string $time): Carbon
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return $bookingDate->copy()->setTime($hour, $minute, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Start Inside Booking Window
    |--------------------------------------------------------------------------
    */

    private function validateStartInsideBookingWindow(Carbon $start, string $date): void
    {
        foreach ($this->getBookingWindows($date) as $window) {
            if ($start->gte($window['opening']) && $start->lt($window['closing'])) {
                return;
            }
        }

        throw ValidationException::withMessages([
            'appointment_time' => 'The selected appointment time is outside the booking hours.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Check Entire Appointment Fits Inside One Window
    |--------------------------------------------------------------------------
    */

    private function fitsInsideBookingWindow(Carbon $start, Carbon $end, string $date): bool
    {
        foreach ($this->getBookingWindows($date) as $window) {
            if ($start->gte($window['opening']) && $end->lte($window['closing'])) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Existing Appointment Range
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | appointment_date = September 30
    | start = 11:30 PM
    | end   = 12:30 AM
    |
    | End is earlier than start, therefore it moves to October 1.
    |
    */

    private function getAppointmentRange(UsersAppointments $appointment): array
    {
        $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

        $start = $this->appointmentDateTime($appointmentDate, $appointment->appointment_time);

        $end = $this->appointmentDateTime($appointmentDate, $appointment->appointment_end_time);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment Overlap
    |--------------------------------------------------------------------------
    */

    private function appointmentOverlaps(Carbon $start, Carbon $end, Carbon $existingStart, Carbon $existingEnd): bool
    {
        return $start->lt($existingEnd) && $end->gt($existingStart);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Appointment
    |--------------------------------------------------------------------------
    */

    private function validateAppointment(UsersAppointments $appointment): void
    {
        if ((int) $appointment->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true)) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment cannot be rescheduled.',
            ]);
        }
    }
}
