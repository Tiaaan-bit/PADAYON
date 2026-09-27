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
    private string $timezone = 'Asia/Manila';

    public function dashboard()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Current Manila Date/Time
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now($this->timezone);
        $today = $now->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Get User Appointments
        |--------------------------------------------------------------------------
        |
        | The dashboard uses:
        | - appointment->user
        | - appointment->service
        | - appointment->therapist
        | - appointment->addOn
        |
        */

        $appointments = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->where('user_id', $user->id)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Build Appointment Start / End DateTime
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Appointment dates now follow the actual calendar date.
        |
        | Example:
        |
        | September 30
        |   12:00 AM - 1:00 AM
        |   1:00 PM  - 12:00 AM
        |
        | October 1
        |   12:00 AM - 1:00 AM
        |   1:00 PM  - 12:00 AM
        |
        | Therefore:
        |
        | September 30 12:30 AM
        | stays September 30.
        |
        | We DO NOT automatically move times before 1 PM
        | to the next day.
        |
        */

        $appointments = $appointments->map(function ($appointment) {
            $date = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

            /*
            |--------------------------------------------------------------------------
            | Appointment Start
            |--------------------------------------------------------------------------
            */

            $start = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . Carbon::parse($appointment->appointment_time, $this->timezone)->format('H:i:s'), $this->timezone);

            $appointment->start_datetime = $start->copy();

            /*
            |--------------------------------------------------------------------------
            | Appointment End
            |--------------------------------------------------------------------------
            */

            if ($appointment->appointment_end_time) {
                $end = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . Carbon::parse($appointment->appointment_end_time, $this->timezone)->format('H:i:s'), $this->timezone);

                /*
                |--------------------------------------------------------------------------
                | Handle Midnight Crossing
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | September 30 11:30 PM
                | ->
                | October 1 12:30 AM
                |
                | Since 12:30 AM is earlier than 11:30 PM,
                | the end belongs to the next calendar day.
                |
                */

                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }

                $appointment->end_datetime = $end;
            } else {
                /*
                |--------------------------------------------------------------------------
                | Fallback Using Duration
                |--------------------------------------------------------------------------
                */

                $duration = (int) ($appointment->service_duration_minutes ?? 0) + (int) ($appointment->addons_duration_minutes ?? 0);

                $appointment->end_datetime = $start->copy()->addMinutes($duration);
            }

            return $appointment;
        });

        /*
        |--------------------------------------------------------------------------
        | Active Appointment Statuses
        |--------------------------------------------------------------------------
        */

        $activeStatuses = [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED];

        /*
        |--------------------------------------------------------------------------
        | Today's Appointments
        |--------------------------------------------------------------------------
        |
        | Show pending and confirmed appointments whose
        | appointment date is today.
        |
        | Past appointments today are still displayed.
        |
        */

        $todayAppointments = $appointments
            ->filter(function ($appointment) use ($today, $activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true) && $appointment->start_datetime->toDateString() === $today;
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
        | - future
        | - not today
        |
        | Maximum: 5
        |
        */

        $upcomingAppointments = $appointments
            ->filter(function ($appointment) use ($today, $now, $activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true) && $appointment->start_datetime->greaterThan($now) && $appointment->start_datetime->toDateString() !== $today;
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
        | Show all pending and confirmed appointments.
        |
        | The calendar uses the real appointment date.
        |
        */

        $calendarAppointments = $appointments
            ->filter(function ($appointment) use ($activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true);
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Calendar Events
        |--------------------------------------------------------------------------
        */

        $calendarEvents = $calendarAppointments
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,

                    /*
                    |--------------------------------------------------------------------------
                    | Real Calendar Date
                    |--------------------------------------------------------------------------
                    */

                    'date' => $appointment->start_datetime->format('Y-m-d'),

                    'title' => $appointment->service->name ?? 'Appointment',

                    'time' => $appointment->start_datetime->format('h:i A'),

                    /*
                    |--------------------------------------------------------------------------
                    | Enum -> String
                    |--------------------------------------------------------------------------
                    */

                    'status' => $appointment->status?->value ?? 'pending',

                    'user' => $appointment->user->name ?? 'User',

                    'therapist' => $appointment->therapist->name ?? 'Not assigned',
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Published Posts / Announcements
        |--------------------------------------------------------------------------
        */

        $posts = Post::query()->whereNotNull('published_at')->where('published_at', '<=', $now)->latest('published_at')->take(5)->get();

        /*
        |--------------------------------------------------------------------------
        | Available Therapists
        |--------------------------------------------------------------------------
        */

        $therapists = Therapists::where('status', 'available')->orderBy('name')->take(6)->get();

        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view('user.dashboard', compact('todayAppointments', 'upcomingAppointments', 'calendarEvents', 'posts', 'therapists'));
    }
}
