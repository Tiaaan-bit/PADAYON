<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersAppointments;
use App\Notifications\AppointmentCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\User\Appointment\RescheduleAppointment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class MyAppointmentController extends Controller
{
    public function __construct(private RescheduleAppointment $rescheduleAppointment) {}

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

        $appointment->update([
            'payment_status' => 'unpaid',
        ]);

        $user->notify(new AppointmentCancelledNotification($appointment));

        return back()->with('success', 'Appointment cancelled successfully.');
    }
    public function showReschedule(UsersAppointments $appointment)
    {
        $user = Auth::user();

        abort_unless($appointment->user_id === $user->id, 403);

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true)) {
            return redirect()->route('user.my-appointments')->with('error', 'This appointment cannot be rescheduled.');
        }

        $appointment->load(['service', 'therapist', 'addOn']);

        return view('user.reschedule-appointment', compact('appointment'));
    }

    public function availableRescheduleSlots(Request $request, UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED], true)) {
            return response()->json(
                [
                    'message' => 'This appointment cannot be rescheduled.',
                ],
                422,
            );
        }

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = $validated['date'];

        $timezone = 'Asia/Manila';

        $selectedDate = Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay();

        $today = Carbon::now($timezone)->startOfDay();

        if ($selectedDate->lt($today)) {
            return response()->json([]);
        }

        $requiredMinutes = (int) $appointment->service_duration_minutes + (int) $appointment->addons_duration_minutes;

        if ($requiredMinutes <= 0) {
            return response()->json(
                [
                    'message' => 'Unable to determine the appointment duration.',
                ],
                422,
            );
        }

        $previousDate = $selectedDate->copy()->subDay()->format('Y-m-d');

        $appointments = UsersAppointments::query()
            ->where('therapist_id', $appointment->therapist_id)
            ->whereIn('appointment_date', [$previousDate, $date])
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $windows = [
            [
                'opening' => $selectedDate->copy()->setTime(0, 0, 0),

                'closing' => $selectedDate->copy()->setTime(1, 0, 0),
            ],

            [
                'opening' => $selectedDate->copy()->setTime(13, 0, 0),

                'closing' => $selectedDate->copy()->addDay()->setTime(0, 0, 0),
            ],
        ];

        $slots = [];

        foreach ($windows as $window) {
            $opening = $window['opening'];
            $closing = $window['closing'];

            for ($slotStart = $opening->copy(); $slotStart->lt($closing); $slotStart->addMinutes(30)) {
                if ($selectedDate->isSameDay(Carbon::now($timezone)->startOfDay()) && $slotStart->lte(Carbon::now($timezone))) {
                    continue;
                }

                $slotEnd = $slotStart->copy()->addMinutes($requiredMinutes);

                if ($slotEnd->gt($closing)) {
                    continue;
                }

                $isAvailable = true;

                foreach ($appointments as $existingAppointment) {
                    $existingRange = $this->getAppointmentRangeForReschedule($existingAppointment);

                    if ($slotStart->lt($existingRange['end']) && $slotEnd->gt($existingRange['start'])) {
                        $isAvailable = false;
                        break;
                    }
                }

                if (!$isAvailable) {
                    continue;
                }

                $slots[] = [
                    'start' => $slotStart->format('H:i'),

                    'end' => $slotEnd->format('H:i'),

                    'label' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),

                    'date_label' => $selectedDate->format('F j, Y'),

                    'status' => 'available',
                ];
            }
        }

        return response()->json($slots);
    }
    private function getAppointmentRangeForReschedule(UsersAppointments $appointment): array
    {
        $date = Carbon::parse($appointment->appointment_date, 'Asia/Manila')->format('Y-m-d');

        $start = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . substr($appointment->appointment_time, 0, 5), 'Asia/Manila');

        $end = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . substr($appointment->appointment_end_time, 0, 5), 'Asia/Manila');

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
    public function reschedule(Request $request, UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date_format:Y-m-d'],
            'appointment_time' => ['required', 'date_format:H:i'],
        ]);

        try {
            $this->rescheduleAppointment->execute($appointment, $validated['appointment_date'], $validated['appointment_time']);

            return redirect()->route('user.my-appointments')->with('success', 'Appointment rescheduled successfully.');
        } catch (ValidationException $e) {
            throw $e;
        }
    }
}
