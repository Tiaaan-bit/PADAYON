<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Current Manila Date/Time
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Get User Appointments
        |--------------------------------------------------------------------------
        |
        | We load the required relationships because the dashboard uses:
        | - appointment->user
        | - appointment->service
        | - appointment->therapist
        | - appointment->addOn
        |
        */

        $appointments = UsersAppointments::with([
            'user',
            'service',
            'therapist',
            'addOn',
        ])
            ->where('user_id', $user->id)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Build Appointment Start / End DateTime
        |--------------------------------------------------------------------------
        |
        | Business hours:
        |
        | 1:00 PM - 1:00 AM
        |
        | Times between 12:00 AM and 12:59 PM belong to
        | the following business day.
        |
        */

        $appointments = $appointments->map(function ($appointment) {
            $date = Carbon::parse($appointment->appointment_date)
                ->format('Y-m-d');

            $time = Carbon::parse($appointment->appointment_time)
                ->format('H:i:s');

            /*
            |--------------------------------------------------------------------------
            | Appointment Start
            |--------------------------------------------------------------------------
            */

            $start = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$date} {$time}",
                'Asia/Manila'
            );

            if ($start->hour < 13) {
                $start->addDay();
            }

            $appointment->start_datetime = $start;

            /*
            |--------------------------------------------------------------------------
            | Appointment End
            |--------------------------------------------------------------------------
            */

            if ($appointment->appointment_end_time) {
                $endTime = Carbon::parse($appointment->appointment_end_time)
                    ->format('H:i:s');

                $end = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    "{$date} {$endTime}",
                    'Asia/Manila'
                );

                if ($end->hour < 13) {
                    $end->addDay();
                }

                $appointment->end_datetime = $end;
            } else {
                $duration =
                    (int) ($appointment->service_duration_minutes ?? 0)
                    + (int) ($appointment->addons_duration_minutes ?? 0);

                $appointment->end_datetime = $start->copy()
                    ->addMinutes($duration);
            }

            return $appointment;
        });

        /*
        |--------------------------------------------------------------------------
        | Active Appointment Statuses
        |--------------------------------------------------------------------------
        |
        | Pending and confirmed appointments are considered active
        | for the user dashboard.
        |
        */

        $activeStatuses = [
            AppointmentStatus::PENDING,
            AppointmentStatus::CONFIRMED,
        ];

        /*
        |--------------------------------------------------------------------------
        | Today's Appointments
        |--------------------------------------------------------------------------
        |
        | Show:
        | - pending
        | - confirmed
        |
        | We intentionally do NOT check whether the appointment
        | is greater than $now because past appointments from
        | today should still appear under "Today's Appointments".
        |
        */

        $todayAppointments = $appointments
            ->filter(function ($appointment) use ($today, $activeStatuses) {
                return in_array(
                    $appointment->status,
                    $activeStatuses,
                    true
                )
                    && $appointment->start_datetime->toDateString() === $today;
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Appointments
        |--------------------------------------------------------------------------
        |
        | Show:
        | - pending
        | - confirmed
        |
        | Conditions:
        | 1. Appointment must be in the future.
        | 2. Appointment must NOT be today.
        | 3. Maximum of 5 appointments.
        |
        */

        $upcomingAppointments = $appointments
            ->filter(function ($appointment) use (
                $today,
                $now,
                $activeStatuses
            ) {
                return in_array(
                    $appointment->status,
                    $activeStatuses,
                    true
                )
                    && $appointment->start_datetime->greaterThan($now)
                    && $appointment->start_datetime->toDateString() !== $today;
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Calendar Appointments
        |--------------------------------------------------------------------------
        |
        | Show ALL pending and confirmed appointments.
        |
        | We intentionally do not filter using $now so the calendar
        | can still display:
        |
        | - today's appointments
        | - today's past appointments
        | - future appointments
        |
        */

        $calendarAppointments = $appointments
            ->filter(function ($appointment) use ($activeStatuses) {
                return in_array(
                    $appointment->status,
                    $activeStatuses,
                    true
                );
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Calendar Events
        |--------------------------------------------------------------------------
        |
        | Convert appointments into JavaScript-friendly objects.
        |
        */

        $calendarEvents = $calendarAppointments
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,

                    'date' => $appointment->start_datetime
                        ->format('Y-m-d'),

                    'title' => $appointment->service->name
                        ?? 'Appointment',

                    'time' => $appointment->start_datetime
                        ->format('h:i A'),

                    // Enum -> string for JavaScript
                    'status' => $appointment->status?->value
                        ?? 'pending',

                    'user' => $appointment->user->name
                        ?? 'User',

                    'therapist' => $appointment->therapist->name
                        ?? 'Not assigned',
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Published Posts / Announcements
        |--------------------------------------------------------------------------
        */

        $posts = Post::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->latest('published_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Therapists
        |--------------------------------------------------------------------------
        */

        $therapists = Therapists::where('status', 'available')
            ->orderBy('name')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view(
            'user.dashboard',
            compact(
                'todayAppointments',
                'upcomingAppointments',
                'calendarEvents',
                'posts',
                'therapists'
            )
        );
    }
}