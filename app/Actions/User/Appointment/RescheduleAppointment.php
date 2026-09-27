<?php

namespace App\Actions\User\Appointment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RescheduleAppointment
{
    private string $timezone = 'Asia/Manila';

    private int $openingHour = 13; // 1:00 PM

    private int $closingHour = 1; // 1:00 AM next day

    public function execute(UsersAppointments $appointment, string $newDate, string $newTime): UsersAppointments
    {
        $this->validateAppointment($appointment);

        $start = $this->appointmentDateTime($newDate, $newTime);

        $window = $this->getBookingWindow($newDate);

        /*
        |--------------------------------------------------------------------------
        | Validate date
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
        | Validate booking hours
        |--------------------------------------------------------------------------
        */

        if ($start->lt($window['opening']) || $start->gte($window['closing'])) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment time is outside the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate past time
        |--------------------------------------------------------------------------
        */

        if ($start->lte($this->manilaNow())) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment time has already passed.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Existing appointment duration
        |--------------------------------------------------------------------------
        |
        | We DO NOT get the current service/add-on again.
        |
        | The existing appointment already stores the duration used when
        | it was originally booked.
        |
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
        | Calculate new end time
        |--------------------------------------------------------------------------
        */

        $end = $start->copy()->addMinutes($totalDuration);

        if ($end->gt($window['closing'])) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment duration extends beyond the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Lock therapist
        |--------------------------------------------------------------------------
        */

        $updatedAppointment = DB::transaction(function () use ($appointment, $newDate, $newTime, $start, $end) {
            $therapist = Therapists::where('id', $appointment->therapist_id)->where('status', 'available')->lockForUpdate()->first();

            if (!$therapist) {
                throw ValidationException::withMessages([
                    'appointment_time' => 'The selected therapist is no longer available.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Get therapist appointments on new date
            |--------------------------------------------------------------------------
            */

            $existingAppointments = UsersAppointments::where('therapist_id', $appointment->therapist_id)
                ->where('appointment_date', $newDate)
                ->where('id', '!=', $appointment->id)
                ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
                ->orderBy('appointment_time')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Final conflict check
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
            | Update ONLY schedule information
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'appointment_date' => $newDate,
                'appointment_time' => $newTime,
                'appointment_end_time' => $end->format('H:i'),
            ]);

            return $appointment->fresh(['service', 'therapist', 'addOn']);
        });

        return $updatedAppointment;
    }

    private function manilaNow(): Carbon
    {
        return Carbon::now($this->timezone);
    }

    private function getBookingWindow(string $date): array
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        $opening = $bookingDate->copy()->setTime($this->openingHour, 0, 0);

        $closing = $bookingDate->copy()->addDay()->setTime($this->closingHour, 0, 0);

        return [
            'opening' => $opening,
            'closing' => $closing,
        ];
    }

    private function appointmentDateTime(string $date, string $time): Carbon
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        /*
        |--------------------------------------------------------------------------
        | 12:00 AM - 12:59 AM belongs to the next calendar day
        |--------------------------------------------------------------------------
        */

        if ($hour === 0) {
            return $bookingDate->copy()->addDay()->setTime($hour, $minute, 0);
        }

        return $bookingDate->copy()->setTime($hour, $minute, 0);
    }

    private function getAppointmentRange(UsersAppointments $appointment): array
    {
        $appointmentDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');

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

    private function appointmentOverlaps(Carbon $start, Carbon $end, Carbon $existingStart, Carbon $existingEnd): bool
    {
        return $start->lt($existingEnd) && $end->gt($existingStart);
    }

    private function validateAppointment(UsersAppointments $appointment): void
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true)) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment cannot be rescheduled.',
            ]);
        }
    }
}
