<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Appointment\CancelAppointment;
use App\Actions\Admin\Appointment\UpdateAppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppointmentStatusRequest;
use App\Models\UsersAppointments;
use App\Repositories\Admin\Appointment\AppointmentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Enums\Admin\Appointment\AppointmentStatus;

class AdminAppointmentsController extends Controller
{
    public function __construct(protected AppointmentRepositoryInterface $appointmentRepository) {}

    public function index(Request $request): View
    {
        $data = $this->appointmentRepository->getAppointmentPageData($request->all());

        return view('admin.appointments', $data);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, UsersAppointments $appointment, UpdateAppointmentStatus $updateAppointmentStatus): RedirectResponse
    {
        Gate::authorize('updateStatus', $appointment);

        $updateAppointmentStatus->execute($appointment, AppointmentStatus::from($request->validated('status')));
        
        return back()->with('success', 'Appointment status updated successfully.');
    }

    public function cancel(UsersAppointments $appointment, CancelAppointment $cancelAppointment): RedirectResponse
    {
        Gate::authorize('cancel', $appointment);

        if ($appointment->status === AppointmentStatus::CANCELLED) {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $cancelAppointment->execute($appointment);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
