<?php

namespace App\Http\Controllers\User;

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
        | IMPORTANT:
        | We load the user relationship because the calendar uses:
        | event.user
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
        */
        $appointments = $appointments->map(function ($appointment) {

            $date = Carbon::parse(
                $appointment->appointment_date
            )->format('Y-m-d');

            $time = Carbon::parse(
                $appointment->appointment_time
            )->format('H:i:s');

            /*
            |--------------------------------------------------------------------------
            | Appointment Start
            |--------------------------------------------------------------------------
            |
            | Your business hours are:
            |
            | 1:00 PM - 1:00 AM
            |
            | Therefore, times before 1:00 PM belong to the next day.
            |
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

                $endTime = Carbon::parse(
                    $appointment->appointment_end_time
                )->format('H:i:s');

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
                    +
                    (int) ($appointment->addons_duration_minutes ?? 0);

                $appointment->end_datetime =
                    $start->copy()->addMinutes($duration);
            }

            return $appointment;
        });

        /*
        |--------------------------------------------------------------------------
        | ACTIVE APPOINTMENT STATUSES
        |--------------------------------------------------------------------------
        |
        | Both pending and confirmed are displayed.
        |
        */
        $activeStatuses = [
            'pending',
            'confirm',
        ];

        /*
        |--------------------------------------------------------------------------
        | TODAY'S APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | Show BOTH:
        | - pending
        | - confirmed
        |
        | We intentionally DO NOT check:
        |
        | start_datetime >= $now
        |
        | because an appointment that happened earlier today should still
        | remain visible under "Today's Appointments".
        |
        */
        $todayAppointments = $appointments
            ->filter(function ($appointment) use (
                $today,
                $activeStatuses
            ) {

                $status = strtolower(
                    trim($appointment->status ?? '')
                );

                return in_array(
                    $status,
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
        | UPCOMING APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | Show pending + confirmed appointments that:
        |
        | 1. Are in the future
        | 2. Are NOT today
        |
        | Limited to 5 appointments.
        |
        */
        $upcomingAppointments = $appointments
            ->filter(function ($appointment) use (
                $today,
                $now,
                $activeStatuses
            ) {

                $status = strtolower(
                    trim($appointment->status ?? '')
                );

                return in_array(
                    $status,
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
        | CALENDAR APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | Show ALL pending + confirmed appointments.
        |
        | We intentionally don't filter by $now here.
        |
        | This means:
        | - Today's appointments show
        | - Today's past appointments show
        | - Future appointments show
        |
        */
        $calendarAppointments = $appointments
            ->filter(function ($appointment) use (
                $activeStatuses
            ) {

                $status = strtolower(
                    trim($appointment->status ?? '')
                );

                return in_array(
                    $status,
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
        | Convert appointments into simple JavaScript-friendly objects.
        |
        */
        $calendarEvents = $calendarAppointments
            ->map(function ($appointment) {

                return [
                    'id' => $appointment->id,

                    'date' => $appointment
                        ->start_datetime
                        ->format('Y-m-d'),

                    'title' => $appointment->service->name
                        ?? 'Appointment',

                    'time' => $appointment
                        ->start_datetime
                        ->format('h:i A'),

                    'status' => strtolower(
                        trim($appointment->status ?? 'pending')
                    ),

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
        $therapists = Therapists::where(
            'status',
            'available'
        )
            ->orderBy('name')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */
        return view('user.dashboard', compact(
            'todayAppointments',
            'upcomingAppointments',
            'calendarEvents',
            'posts',
            'therapists'
        ));
    }
}