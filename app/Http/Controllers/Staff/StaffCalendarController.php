<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffCalendarController extends Controller
{
    public function index()
    {
        return view('calendars.index', [
            'eventsUrl' => route('staff.calendar.events'),
            'layout' => 'layouts.staff',
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        $start = Carbon::parse($validated['start'], 'Asia/Manila')->toDateString();

        // FullCalendar's end date is exclusive.
        $end = Carbon::parse($validated['end'], 'Asia/Manila')->subDay()->toDateString();

        $appointments = UsersAppointments::query()
            ->with(['user', 'service', 'therapist', 'addOn'])
            ->whereDate('appointment_date', '<=', $end)
            ->whereDate('appointment_date', '>=', $start)
            ->get();

        $events = $appointments->map(function ($appointment) {
            $status = $appointment->status;

            $status = is_object($status) ? $status->value ?? ($status->name ?? 'pending') : (string) $status;

            $status = strtolower($status);

            $colors = [
                'pending' => '#D97706',
                'confirmed' => '#16A34A',
                'cancelled' => '#6B7280',
                'canceled' => '#6B7280',
                'rejected' => '#DC2626',
                'no_show' => '#7C3AED',
                'failed' => '#DC2626',
            ];

            $date = Carbon::parse($appointment->appointment_date, 'Asia/Manila')->toDateString();

            $startTime = Carbon::parse($appointment->appointment_time, 'Asia/Manila')->format('H:i:s');

            $endTime = $appointment->appointment_end_time ? Carbon::parse($appointment->appointment_end_time, 'Asia/Manila')->format('H:i:s') : null;

            return [
                'id' => $appointment->id,

                'title' => ($appointment->user?->name ?? ($appointment->walkInCustomer?->name ?? 'Walk-in / Guest')) . ' — ' . ($appointment->service?->name ?? 'Appointment'),

                'start' => "{$date}T{$startTime}",

                'end' => $endTime ? "{$date}T{$endTime}" : null,

                'backgroundColor' => $colors[$status] ?? '#718343',

                'borderColor' => $colors[$status] ?? '#718343',

                'extendedProps' => [
                    'customer' => $appointment->user?->name ?? ($appointment->walkInCustomer?->name ?? 'Walk-in / Guest'),

                    'service' => $appointment->service?->name ?? 'Not specified',
                    
                    'service_description' => $appointment->service?->description ?? 'No description available',

                    'service_duration_minutes' => $appointment->service_duration_minutes ?? ($appointment->service?->duration_minutes ?? null),

                    'therapist' => $appointment->therapist?->name ?? 'Not assigned',

                    'status' => ucfirst(str_replace('_', ' ', $status)),

                    'payment_method' => $appointment->payment_method ?? 'Not specified',

                    'payment_status' => $appointment->payment_status ?? 'Not specified',
                ],
            ];
        });

        return response()->json($events);
    }
}
