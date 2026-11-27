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

    protected $description = 'Send 24-hour and 3-hour appointment reminders';

    public function handle(): int
    {
        $timezone = 'Asia/Manila';
        $now = Carbon::now($timezone)->startOfMinute();

        $reminders = [
            [
                'minutes' => 1440,
                'column' => 'reminder_24h_sent_at',
                'label' => '24 hours',
            ],
            [
                'minutes' => 180,
                'column' => 'reminder_3h_sent_at',
                'label' => '3 hours',
            ],
        ];

        $appointments = UsersAppointments::query()
            ->with('user')
            ->whereIn('status', [
                AppointmentStatus::CONFIRMED->value,
                AppointmentStatus::PENDING->value,
            ])
            ->whereBetween('appointment_date', [
                $now->copy()->addHours(3)->toDateString(),
                $now->copy()->addHours(24)->addMinutes(5)->toDateString(),
            ])
            ->get();

        foreach ($appointments as $appointment) {
            if (!$appointment->user) {
                continue;
            }

            $appointmentDate = Carbon::parse(
                $appointment->appointment_date->toDateString()
                    . ' ' . $appointment->appointment_time,
                $timezone
            );

            // Never send a reminder after the appointment has started.
            if (!$appointmentDate->isFuture()) {
                continue;
            }

            foreach ($reminders as $reminder) {
                $column = $reminder['column'];

                // Prevent sending the same reminder twice.
                if ($appointment->{$column}) {
                    continue;
                }

                // Calculate the exact reminder due time.
                $dueAt = $appointmentDate->copy()
                    ->subMinutes($reminder['minutes']);

                // Allow delivery from the due time up to 5 minutes later.
                $gracePeriodEnds = $dueAt->copy()->addMinutes(5);

                if (
                    $now->lt($dueAt) ||
                    !$now->lt($gracePeriodEnds)
                ) {
                    continue;
                }

                try {
                    $appointment->user->notify(
                        new AppointmentReminderNotification(
                            $appointment,
                            $reminder['label']
                        )
                    );

                    $appointment->{$column} = $now;
                    $appointment->save();

                    $this->info(
                        "{$reminder['label']} reminder sent for appointment #{$appointment->id}"
                    );
                } catch (Throwable $exception) {
                    report($exception);

                    $this->error(
                        "Failed to send {$reminder['label']} reminder for appointment #{$appointment->id}"
                    );
                }
            }
        }

        return self::SUCCESS;
    }
}
