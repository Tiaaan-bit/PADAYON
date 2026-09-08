<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today('Asia/Manila');

        /*
        |--------------------------------------------------------------------------
        | TODAY'S APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | This collection is ONLY for the Today's Appointments table.
        |
        */

        $appointments = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TODAY'S APPOINTMENT COUNT
        |--------------------------------------------------------------------------
        */

        $todayAppointmentsCount = UsersAppointments::whereDate('appointment_date', $today)->count();

        /*
        |--------------------------------------------------------------------------
        | TODAY'S CONFIRMED APPOINTMENTS
        |--------------------------------------------------------------------------
        */

        $confirmedAppointmentsCount = UsersAppointments::whereDate('appointment_date', $today)->where('status', 'confirm')->count();

        /*
        |--------------------------------------------------------------------------
        | TODAY'S PENDING APPOINTMENTS
        |--------------------------------------------------------------------------
        */

        $pendingAppointmentsCount = UsersAppointments::whereDate('appointment_date', $today)->where('status', 'pending')->count();

        /*
        |--------------------------------------------------------------------------
        | THERAPISTS ON DUTY
        |--------------------------------------------------------------------------
        */

        $therapists = Therapists::where('status', 'available')->get();

        /*
        |--------------------------------------------------------------------------
        | UPCOMING APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | Includes today's and future appointments.
        | Only pending and confirmed appointments are shown.
        |
        */

        $upcomingAppointments = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', '>', $today)
            ->whereIn('status', ['pending', 'confirm'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ANNOUNCEMENTS
        |--------------------------------------------------------------------------
        */

        $posts = Post::whereNotNull('published_at')->latest('published_at')->get();

        /*
        |--------------------------------------------------------------------------
        | CALENDAR EVENTS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | The calendar uses ALL appointments, not only today's appointments.
        |
        */

        $allAppointmentsForCalendar = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $calendarEvents = $allAppointmentsForCalendar
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,

                    'date' => Carbon::parse($appointment->appointment_date)->format('Y-m-d'),

                    'time' => Carbon::parse($appointment->appointment_time)->format('h:i A'),

                    'title' => $appointment->service->name ?? 'Service',

                    'user' => $appointment->user->name ?? 'N/A',

                    'therapist' => $appointment->therapist->name ?? 'N/A',

                    'status' => strtolower(trim($appointment->status ?? '')),
                ];
            })
            ->values();
        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD VIEW
        |--------------------------------------------------------------------------
        */

        return view('staff.dashboard', compact('appointments', 'therapists', 'upcomingAppointments', 'posts', 'calendarEvents', 'todayAppointmentsCount', 'confirmedAppointmentsCount', 'pendingAppointmentsCount'));
    }
}
