<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TherapistDashboardController extends Controller
{
    public function dashboard()
    {
        // Get the currently logged-in therapist
        $therapist = Auth::guard('therapist')->user();

        // Use the same timezone as the admin dashboard
        $today = Carbon::now('Asia/Manila')->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Today's Appointments
        |--------------------------------------------------------------------------
        | Only appointments assigned to the logged-in therapist
        | and confirmed by the admin.
        */
        $todayAppointments = $therapist->appointments()
            ->with([
                'user',
                'service',
                'addOn',
            ])
            ->whereDate('appointment_date', $today)
            ->where('status', 'confirm')
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Appointments
        |--------------------------------------------------------------------------
        | Future appointments assigned to the logged-in therapist.
        */
        $upcomingAppointments = $therapist->appointments()
            ->with([
                'user',
                'service',
                'addOn',
            ])
            ->whereDate('appointment_date', '>', $today)
            ->where('status', 'confirm')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Total Unique Clients
        |--------------------------------------------------------------------------
        */
        $totalClients = $therapist->appointments()
            ->where('status', 'confirm')
            ->distinct('user_id')
            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | Calendar Events
        |--------------------------------------------------------------------------
        */
        $calendarEvents = $therapist->appointments()
            ->with('service')
            ->where('status', 'confirm')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($appointment) {
                return [
                    'date' => Carbon::parse(
                        $appointment->appointment_date
                    )->format('Y-m-d'),

                    'time' => Carbon::parse(
                        $appointment->appointment_time
                    )->format('h:i A'),

                    'title' => $appointment->service->name
                        ?? 'Appointment',
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Return Therapist Dashboard
        |--------------------------------------------------------------------------
        */
        return view('therapist.dashboard', compact(
            'therapist',
            'todayAppointments',
            'upcomingAppointments',
            'totalClients',
            'calendarEvents'
        ));
    }
}
