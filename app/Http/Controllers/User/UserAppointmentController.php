<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AddOns;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAppointmentController extends Controller
{
    public function index()
{
    $servicesQuery = Services::where('status', 'active');

    if (request('category')) {
        $keyword = request('category');

        $servicesQuery->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%");
        });
    }

    $services = $servicesQuery->orderBy('name')->get();

    $therapists = Therapists::where('status', 'available')->orderBy('name')->get();
    $addOns = AddOns::where('status', 'active')->orderBy('name')->get();

    return view('user.appointment', compact('services', 'therapists', 'addOns'));
}

    public function create()
    {
        return $this->index();
    }

    private function manilaNow(): Carbon
    {
        return Carbon::now('Asia/Manila');
    }

    private function getScheduleBounds(string $date, int $duration): array
    {
        $open = Carbon::parse($date . ' 13:00:00', 'Asia/Manila');
        $close = Carbon::parse($date . ' 01:00:00', 'Asia/Manila')->addDay();
        $latestStart = $close->copy()->subMinutes($duration);

        return [$open, $close, $latestStart];
    }

    private function buildAppointmentDateTime(string $date, string $time): Carbon
    {
        $dt = Carbon::parse($date . ' ' . $time, 'Asia/Manila');

        if (Carbon::parse($time)->format('H') === '00') {
            $dt->addDay();
        }

        return $dt;
    }

    private function getBookingStartEnd($booking): array
    {
        $bookingDate = Carbon::parse($booking->appointment_date, 'Asia/Manila')->format('Y-m-d');
        $start = $this->buildAppointmentDateTime($bookingDate, $booking->appointment_time);

        $end = !empty($booking->appointment_end_time)
            ? $this->buildAppointmentDateTime($bookingDate, $booking->appointment_end_time)
            : $start->copy()->addMinutes((int) ($booking->service_duration_minutes ?? 0) + (int) ($booking->addons_duration_minutes ?? 0));

        if ($end->lt($start)) {
            $end->addDay();
        }

        return [$start, $end];
    }

    private function hasConflict($therapistId, Carbon $start, Carbon $end): bool
    {
        return UsersAppointments::where('therapist_id', $therapistId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->contains(function ($booking) use ($start, $end) {
                [$existingStart, $existingEnd] = $this->getBookingStartEnd($booking);
                return $start < $existingEnd && $end > $existingStart;
            });
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'add_on_id' => 'nullable|exists:add_ons,id',
        ]);

        $service = Services::findOrFail($request->service_id);
        $addOn = !empty($request->add_on_id) ? AddOns::findOrFail($request->add_on_id) : null;

        $duration = (int) $service->duration_minutes + (int) ($addOn->duration_minutes ?? 0);
        $slotStep = 30;

        $date = Carbon::parse($request->date, 'Asia/Manila')->format('Y-m-d');
        [$open, $close, $latestStart] = $this->getScheduleBounds($date, $duration);

        $bookings = UsersAppointments::where('therapist_id', $request->therapist_id)
            ->where('status', '!=', 'cancelled')
            ->get();

        $slots = [];
        $current = $open->copy();
        $now = $this->manilaNow();

        while ($current->lte($latestStart)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($duration);

            if ($slotStart->format('H') === '00') {
                $slotStart->addDay();
                $slotEnd->addDay();
            }

            $isBooked = false;

            foreach ($bookings as $booking) {
                [$existingStart, $existingEnd] = $this->getBookingStartEnd($booking);

                if ($slotStart < $existingEnd && $slotEnd > $existingStart) {
                    $isBooked = true;
                    break;
                }
            }

            $isPastToday = $date === $now->format('Y-m-d') && $slotStart->lt($now);

            if (!$isPastToday) {
                $slots[] = [
                    'start' => $slotStart->format('H:i:s'),
                    'label' => $slotStart->format('h:i A') . ' - ' . $slotEnd->format('h:i A'),
                    'status' => $isBooked ? 'booked' : 'available',
                ];
            }

            $current->addMinutes($slotStep);
        }

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'therapist_id' => 'required|exists:therapists,id',
            'level' => 'required|in:gentle,mild,hard',
            'add_on_id' => 'nullable|exists:add_ons,id',
            'has_previous_operations' => 'nullable|in:yes,no',
            'body_problem' => 'nullable|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'payment_method' => 'required|in:branch,gcash',
            'payment_type' => 'nullable|in:full,downpayment',
        ]);

        $service = Services::findOrFail($validated['service_id']);
        $therapist = Therapists::findOrFail($validated['therapist_id']);
        $addOn = !empty($validated['add_on_id']) ? AddOns::findOrFail($validated['add_on_id']) : null;

        if ($service->status !== 'active') {
            return back()->withErrors(['service_id' => 'Selected service is inactive.'])->withInput();
        }

        if ($therapist->status !== 'available') {
            return back()->withErrors(['therapist_id' => 'Selected therapist is unavailable.'])->withInput();
        }

        if ($addOn && $addOn->status !== 'active') {
            return back()->withErrors(['add_on_id' => 'Selected add-on is inactive.'])->withInput();
        }

        $date = Carbon::parse($validated['appointment_date'], 'Asia/Manila')->format('Y-m-d');
        $start = $this->buildAppointmentDateTime($date, $validated['appointment_time']);
        $totalMinutes = (int) $service->duration_minutes + (int) ($addOn->duration_minutes ?? 0);
        $end = $start->copy()->addMinutes($totalMinutes);

        $now = $this->manilaNow();

        if ($date === $now->format('Y-m-d') && $start->lt($now)) {
            return back()->withErrors([
                'appointment_time' => 'You cannot book a time that has already passed today.',
            ])->withInput();
        }

        [$open, $close, $latestStart] = $this->getScheduleBounds($date, $totalMinutes);

        if ($start->lt($open) || $start->gt($latestStart) || $end->gt($close)) {
            return back()->withErrors([
                'appointment_time' => 'Appointment must be within 1:00 PM to 1:00 AM, and the full service time must fit inside that window.',
            ])->withInput();
        }

        if ($this->hasConflict((int) $validated['therapist_id'], $start, $end)) {
            return back()->withErrors([
                'appointment_time' => 'That time range is already booked.',
            ])->withInput();
        }

        $servicePrice = (float) $service->price;
        $addOnPrice = $addOn ? (float) $addOn->price : 0;
        $amountPaid = 0;
        $paymentType = null;

        if ($validated['payment_method'] === 'gcash') {
            $paymentType = $validated['payment_type'] ?? 'full';
            $amountPaid = $paymentType === 'downpayment'
                ? ($servicePrice + $addOnPrice) * 0.5
                : $servicePrice + $addOnPrice;
        }

        UsersAppointments::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'therapist_id' => $therapist->id,
            'service_price' => $servicePrice,
            'service_duration_minutes' => $service->duration_minutes,
            'level' => $validated['level'],
            'add_on_id' => $addOn?->id,
            'addons_price' => $addOnPrice,
            'addons_duration_minutes' => $addOn?->duration_minutes ?? 0,
            'has_previous_operations' => $validated['has_previous_operations'] ?? null,
            'body_problem' => $validated['body_problem'] ?? null,
            'appointment_date' => $date,
            'appointment_time' => $start->format('H:i:s'),
            'appointment_end_time' => $end->format('H:i:s'),
            'payment_method' => $validated['payment_method'],
            'payment_type' => $paymentType,
            'amount_paid' => $amountPaid,
            'status' => 'pending',
        ]);

        return redirect()->route('user.appointment')->with(
            'success',
            'Your booking request has been successfully submitted and is currently pending confirmation. We will notify you once your appointment has been approved.'
        );
    }

    
}