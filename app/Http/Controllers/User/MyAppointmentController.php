<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersAppointments;
use App\Notifications\AppointmentCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $appointmentsQuery = UsersAppointments::with(['service', 'therapist', 'addOn'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $appointmentsQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $appointmentsQuery->where(function ($q) use ($search) {
                $q->whereHas('service', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('therapist', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('level', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('appointment_date')) {
            $appointmentsQuery->whereDate('appointment_date', $request->appointment_date);
        }

        $appointments = $appointmentsQuery->orderByDesc('appointment_date')->orderByDesc('appointment_time')->paginate(5)->withQueryString();

        $statsQuery = UsersAppointments::where('user_id', $user->id);

        $totalAppointments = (clone $statsQuery)->count();
        $pendingAppointments = (clone $statsQuery)->where('status', 'pending')->count();
        $confirmedAppointments = (clone $statsQuery)->where('status', 'confirm')->count();
        $rejectedAppointments = (clone $statsQuery)->where('status', 'rejected')->count();
        $cancelledAppointments = (clone $statsQuery)->where('status', 'cancelled')->count();

        return view('user.myappointments', compact('appointments', 'totalAppointments', 'pendingAppointments', 'confirmedAppointments', 'rejectedAppointments', 'cancelledAppointments'));
    }

    public function cancel(UsersAppointments $appointment)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        if ($appointment->user_id !== $user->id) {
            abort(403);
        }

        if ($appointment->status === AppointmentStatus::CANCELLED) {
            return back()->with('error', 'This appointment is already cancelled.');
        }

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true)) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
        ]);

        $user->notify(new AppointmentCancelledNotification($appointment));

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
