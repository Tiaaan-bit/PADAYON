<?php

namespace App\Console\Commands;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\UsersAppointments;
use App\Notifications\AppointmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send reminders for upcoming confirmed appointments';

    public function handle(): int
    {
        $timezone = 'Asia/Manila';
        $now = Carbon::now($timezone);

        $windowStart = $now->copy()->addHours(24);
        $windowEnd = $windowStart->copy()->addMinute();

        $appointments = UsersAppointments::query()
            ->with('user')
            ->where('status', AppointmentStatus::CONFIRMED->value)
            ->whereNull('reminder_sent_at')
            ->whereBetween('appointment_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->get();

        /** @var UsersAppointments $appointment */
        foreach ($appointments as $appointment) {
            if (!$appointment->user) {
                continue;
            }

            $appointmentDate = Carbon::parse(Carbon::parse($appointment->appointment_date, $timezone)->toDateString() . ' ' . $appointment->appointment_time, $timezone);

            if ($appointmentDate->lt($windowStart) || $appointmentDate->gt($windowEnd)) {
                continue;
            }

            try {
                $appointment->user->notify(new AppointmentReminderNotification($appointment));

                $appointment->reminder_sent_at = $now;
                $appointment->save();

                $this->info("Reminder sent for appointment #{$appointment->id}");
            } catch (Throwable $exception) {
                report($exception);

                $this->error("Failed to send reminder for appointment #{$appointment->id}");
            }
        }

        return self::SUCCESS;
    }
}
