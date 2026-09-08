<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | TODAY
        |--------------------------------------------------------------------------
        */
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
        |
        | Your database uses "confirm".
        |
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
        | IMPORTANT:
        | Only FUTURE dates are included.
        |
        | Today's appointments are NOT included here.
        |
        | Status:
        | - pending
        | - confirm
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
        $posts = Post::whereNotNull('published_at')->where('published_at', '<=', Carbon::now('Asia/Manila'))->latest('published_at')->get();

        /*
|--------------------------------------------------------------------------
| CALENDAR EVENTS
|--------------------------------------------------------------------------
|
| The calendar uses all appointments.
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
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */
        return view('admin.dashboard', compact('appointments', 'therapists', 'upcomingAppointments', 'posts', 'calendarEvents', 'todayAppointmentsCount', 'confirmedAppointmentsCount', 'pendingAppointmentsCount'));
    }
}
