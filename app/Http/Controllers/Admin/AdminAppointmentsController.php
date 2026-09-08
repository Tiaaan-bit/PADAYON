<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentNoShowNotification;
use App\Notifications\AppointmentRejectedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $totalAppointments = UsersAppointments::count();
        $totalConfirmed = UsersAppointments::where('status', 'confirm')->count();
        $totalRejected = UsersAppointments::where('status', 'rejected')->count();
        $totalPending = UsersAppointments::where('status', 'pending')->count();
        $totalCancelled = UsersAppointments::where('status', 'cancelled')->count();
        $totalNoShow = UsersAppointments::where('status', 'no show')->count();


        $query = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service')) {
            $query->where('service_id', $request->service);
        }

        if ($request->filled('appointment_date')) {
            $query->whereDate('appointment_date', $request->appointment_date);
        }

        $appointments = $query->paginate(5)->withQueryString();

        $services = \App\Models\Services::orderBy('name')->get();

        return view('admin.appointments', compact('totalAppointments', 'totalConfirmed', 'totalRejected', 'totalPending', 'totalCancelled', 'appointments', 'services','totalNoShow'));
    }

    public function updateStatus(Request $request, UsersAppointments $appointment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirm,rejected,cancelled,no show'],
        ]);

        $oldStatus = $appointment->status;

        $appointment->update([
            'status' => $validated['status'],
        ]);

        $user = $appointment->user;

        if ($user && $validated['status'] !== $oldStatus) {
            if ($validated['status'] === 'confirm') {
                $user->notify(new AppointmentConfirmedNotification($appointment));
            }

            if ($validated['status'] === 'rejected') {
                $user->notify(new AppointmentRejectedNotification($appointment));
            }

            if ($validated['status'] === 'cancelled') {
                $user->notify(new AppointmentCancelledNotification($appointment));
            }

            if ($validated['status'] === 'no show') {
                $user->notify(new AppointmentNoShowNotification($appointment));
            }
        }

        return back()->with('success', 'Appointment status updated successfully.');
    }

    public function cancel(UsersAppointments $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
