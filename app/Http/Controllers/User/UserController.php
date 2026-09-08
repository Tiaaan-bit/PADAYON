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

        // Padayon uses Manila time
        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Get User Appointments
        |--------------------------------------------------------------------------
        */

        $appointments = UsersAppointments::with([
            'service',
            'therapist',
            'addOn',
        ])
            ->where('user_id', $user->id)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Prepare Appointment Date/Time
        |--------------------------------------------------------------------------
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

            /*
            |--------------------------------------------------------------------------
            | Your business hours are 1 PM - 1 AM.
            |
            | Therefore:
            | 12:00 AM - 12:59 AM belongs to the next day.
            |--------------------------------------------------------------------------
            */

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
        | Appointment Statuses That Are Still Active
        |--------------------------------------------------------------------------
        */

        $activeStatuses = [
            'pending',
            'confirmed',
        ];


        /*
        |--------------------------------------------------------------------------
        | TODAY'S APPOINTMENTS
        |--------------------------------------------------------------------------
        */

        $todayAppointments = $appointments
            ->filter(function ($appointment) use (
                $today,
                $now,
                $activeStatuses
            ) {

                return in_array(
                    strtolower($appointment->status ?? ''),
                    $activeStatuses,
                    true
                )
                && $appointment->start_datetime->toDateString() === $today
                && $appointment->start_datetime->greaterThanOrEqualTo($now);
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
        | Future appointments excluding today's appointments.
        |--------------------------------------------------------------------------
        */

        $upcomingAppointments = $appointments
            ->filter(function ($appointment) use (
                $today,
                $now,
                $activeStatuses
            ) {

                return in_array(
                    strtolower($appointment->status ?? ''),
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
        */

        $calendarAppointments = $appointments
            ->filter(function ($appointment) use (
                $now,
                $activeStatuses
            ) {

                return in_array(
                    strtolower($appointment->status ?? ''),
                    $activeStatuses,
                    true
                )
                && $appointment->start_datetime->greaterThanOrEqualTo($now);
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | CALENDAR EVENTS
        |--------------------------------------------------------------------------
        */

        $calendarEvents = $calendarAppointments
            ->map(function ($appointment) {

                return [
                    'date' => $appointment->start_datetime->format('Y-m-d'),

                    'title' => $appointment->service->name
                        ?? 'Appointment',

                    'time' => $appointment->start_datetime->format('h:i A'),

                    'status' => strtolower(
                        $appointment->status ?? 'pending'
                    ),
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ANNOUNCEMENTS
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
        | AVAILABLE THERAPISTS
        |--------------------------------------------------------------------------
        */

        $therapists = Therapists::where('status', 'available')
            ->orderBy('name')
            ->take(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dashboard
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