<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Staff\Appointment\StaffCancelAppointment;
use App\Actions\Staff\Appointment\StaffUpdateAppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppointmentStatusRequest;
use App\Models\UsersAppointments;
use App\Repositories\Staff\Appointment\StaffAppointmentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Enums\Admin\Appointment\AppointmentStatus;

class StaffAppointmentsController extends Controller
{
    public function __construct(protected StaffAppointmentRepositoryInterface $appointmentRepository) {}

    public function index(Request $request): View
    {
        $data = $this->appointmentRepository->getAppointmentPageData($request->all());

        return view('staff.appointments', $data);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, UsersAppointments $appointment, StaffUpdateAppointmentStatus $updateAppointmentStatus): RedirectResponse
    {
        Gate::authorize('staff.appointment.updateStatus', $appointment);

        $updateAppointmentStatus->execute($appointment, AppointmentStatus::from($request->validated('status')));

        return back()->with('success', 'Appointment status updated successfully.');
    }

    public function cancel(UsersAppointments $appointment, StaffCancelAppointment $cancelAppointment): RedirectResponse
    {
        Gate::authorize('staff.appointment.cancel', $appointment);
        
        if ($appointment->status === AppointmentStatus::CANCELLED) {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $cancelAppointment->execute($appointment);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
