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
use Illuminate\Validation\ValidationException;

class UserAppointmentController extends Controller
{
    private string $timezone = 'Asia/Manila';

    private int $openingHour = 13; // 1:00 PM
    private int $closingHour = 1; // 1:00 AM next day
    private int $slotInterval = 30;

    public function index()
    {
        $services = Services::where('status', 'active')->orderBy('name')->get();

        $therapists = Therapists::where('status', 'available')->orderBy('name')->get();

        $addOns = AddOns::where('status', 'active')->orderBy('name')->get();

        return view('user.appointment', compact('services', 'therapists', 'addOns'));
    }

    private function manilaNow(): Carbon
    {
        return Carbon::now($this->timezone);
    }

    /*
    |--------------------------------------------------------------------------
    | Get booking window
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | September 16
    | 1:00 PM
    |     ↓
    | September 17
    | 1:00 AM
    |
    */

    private function getBookingWindow(string $date): array
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        $opening = $bookingDate->copy()->setTime($this->openingHour, 0, 0);

        $closing = $bookingDate->copy()->addDay()->setTime($this->closingHour, 0, 0);

        return [
            'opening' => $opening,
            'closing' => $closing,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Convert appointment time to datetime
    |--------------------------------------------------------------------------
    |
    | 12:00 AM - 12:59 AM belongs to the next calendar day.
    |
    */

    private function appointmentDateTime(string $date, string $time): Carbon
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        if ($hour === 0) {
            return $bookingDate->copy()->addDay()->setTime($hour, $minute, 0);
        }

        return $bookingDate->copy()->setTime($hour, $minute, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Get existing appointments for therapist
    |--------------------------------------------------------------------------
    |
    | Cancelled and rejected appointments do not block the therapist.
    |
    */

    private function getTherapistAppointments(int $therapistId, string $date)
    {
        return UsersAppointments::where('therapist_id', $therapistId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('appointment_time')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Convert existing appointment to datetime range
    |--------------------------------------------------------------------------
    */

    private function getAppointmentRange(UsersAppointments $appointment): array
    {
        $appointmentDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');

        $start = $this->appointmentDateTime($appointmentDate, $appointment->appointment_time);

        $end = $this->appointmentDateTime($appointmentDate, $appointment->appointment_end_time);

        /*
        |--------------------------------------------------------------------------
        | If end is before/equal to start,
        | the appointment crosses midnight.
        |--------------------------------------------------------------------------
        */

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Check appointment overlap
    |--------------------------------------------------------------------------
    */

    private function appointmentOverlaps(Carbon $start, Carbon $end, Carbon $existingStart, Carbon $existingEnd): bool
    {
        return $start->lt($existingEnd) && $end->gt($existingStart);
    }

    /*
    |--------------------------------------------------------------------------
    | Find next booking after current slot
    |--------------------------------------------------------------------------
    */

    private function getNextBookingStart(Carbon $slotStart, array $appointments, Carbon $closing): Carbon
    {
        $nextBooking = $closing->copy();

        foreach ($appointments as $appointment) {
            $range = $this->getAppointmentRange($appointment);

            if ($range['start']->gt($slotStart) && $range['start']->lt($nextBooking)) {
                $nextBooking = $range['start']->copy();
            }
        }

        return $nextBooking;
    }

    /*
    |--------------------------------------------------------------------------
    | Recommended services
    |--------------------------------------------------------------------------
    */

    private function getRecommendedServices(int $availableMinutes, ?AddOns $selectedAddOn, int $currentServiceId): array
    {
        $addOnDuration = $selectedAddOn ? (int) $selectedAddOn->duration_minutes : 0;

        $addOnPrice = $selectedAddOn ? (float) $selectedAddOn->price : 0;

        $availableServiceMinutes = $availableMinutes - $addOnDuration;

        if ($availableServiceMinutes <= 0) {
            return [];
        }

        return Services::where('status', 'active')
            ->where('id', '!=', $currentServiceId)
            ->where('duration_minutes', '<=', $availableServiceMinutes)
            ->orderBy('duration_minutes')
            ->orderBy('price')
            ->get()
            ->map(function ($service) use ($addOnDuration, $addOnPrice) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => (float) $service->price,
                    'duration_minutes' => (int) $service->duration_minutes,
                    'total_duration' => (int) $service->duration_minutes + $addOnDuration,
                    'total_price' => (float) $service->price + $addOnPrice,
                ];
            })
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Available slots
    |--------------------------------------------------------------------------
    */

    public function availableSlots(Request $request)
    {
        $validated = $request->validate([
            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],

            'service_id' => ['required', 'integer', 'exists:services,id'],

            'add_on_id' => ['nullable', 'integer', 'exists:add_ons,id'],

            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get selected service
        |--------------------------------------------------------------------------
        */

        $service = Services::where('id', $validated['service_id'])->where('status', 'active')->first();

        if (!$service) {
            return response()->json(
                [
                    'message' => 'Selected service is no longer available.',
                ],
                422,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get therapist
        |--------------------------------------------------------------------------
        */

        $therapist = Therapists::where('id', $validated['therapist_id'])->where('status', 'available')->first();

        if (!$therapist) {
            return response()->json(
                [
                    'message' => 'Selected therapist is no longer available.',
                ],
                422,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get add-on
        |--------------------------------------------------------------------------
        */

        $addOn = null;

        if (!empty($validated['add_on_id'])) {
            $addOn = AddOns::where('id', $validated['add_on_id'])->where('status', 'active')->first();

            if (!$addOn) {
                return response()->json(
                    [
                        'message' => 'Selected add-on is no longer available.',
                    ],
                    422,
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Total required duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) $service->duration_minutes;

        $addOnDuration = $addOn ? (int) $addOn->duration_minutes : 0;

        $requiredMinutes = $serviceDuration + $addOnDuration;

        /*
        |--------------------------------------------------------------------------
        | Booking window
        |--------------------------------------------------------------------------
        */

        $window = $this->getBookingWindow($validated['date']);

        $opening = $window['opening'];
        $closing = $window['closing'];

        /*
        |--------------------------------------------------------------------------
        | Existing therapist appointments
        |--------------------------------------------------------------------------
        */

        $appointments = $this->getTherapistAppointments((int) $validated['therapist_id'], $validated['date']);

        /*
        |--------------------------------------------------------------------------
        | Current Manila time
        |--------------------------------------------------------------------------
        */

        $now = $this->manilaNow();

        /*
        |--------------------------------------------------------------------------
        | Generate slots
        |--------------------------------------------------------------------------
        */

        $slots = [];

        for ($slotStart = $opening->copy(); $slotStart->lt($closing); $slotStart->addMinutes($this->slotInterval)) {
            /*
            |--------------------------------------------------------------------------
            | Don't show past times.
            |--------------------------------------------------------------------------
            */

            if ($slotStart->isSameDay($now) && $slotStart->lessThanOrEqualTo($now)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Actual 30-minute block represented by this slot.
            |--------------------------------------------------------------------------
            */

            $slotBlockEnd = $slotStart->copy()->addMinutes($this->slotInterval);

            /*
            |--------------------------------------------------------------------------
            | Check whether another appointment occupies
            | this exact 30-minute slot.
            |--------------------------------------------------------------------------
            */

            $isBooked = false;

            foreach ($appointments as $appointment) {
                $range = $this->getAppointmentRange($appointment);

                if ($this->appointmentOverlaps($slotStart, $slotBlockEnd, $range['start'], $range['end'])) {
                    $isBooked = true;
                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BOOKED
            |--------------------------------------------------------------------------
            */

            if ($isBooked) {
                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'label' => $slotStart->format('g:i A') . ' - ' . $slotBlockEnd->format('g:i A'),
                    'status' => 'booked',
                    'available_minutes' => 0,
                    'required_minutes' => $requiredMinutes,
                    'message' => 'This time is already reserved by another appointment.',
                    'recommendations' => [],
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Find next appointment.
            |--------------------------------------------------------------------------
            */

            $nextBookingStart = $this->getNextBookingStart($slotStart, $appointments->all(), $closing);

            /*
            |--------------------------------------------------------------------------
            | Continuous free minutes
            |--------------------------------------------------------------------------
            */

            $availableMinutes = $slotStart->diffInMinutes($nextBookingStart);

            /*
            |--------------------------------------------------------------------------
            | Full service fits
            |--------------------------------------------------------------------------
            */

            if ($availableMinutes >= $requiredMinutes) {
                $slotEnd = $slotStart->copy()->addMinutes($requiredMinutes);

                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'label' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'status' => 'available',
                    'available_minutes' => $availableMinutes,
                    'required_minutes' => $requiredMinutes,
                    'message' => null,
                    'recommendations' => [],
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Some time exists but not enough for selected service.
            |--------------------------------------------------------------------------
            */

            if ($availableMinutes > 0) {
                $recommendations = $this->getRecommendedServices($availableMinutes, $addOn, (int) $service->id);

                $freeEnd = $slotStart->copy()->addMinutes($availableMinutes);

                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'label' => $slotStart->format('g:i A') . ' - ' . $freeEnd->format('g:i A'),
                    'status' => 'adjust_service',
                    'available_minutes' => $availableMinutes,
                    'required_minutes' => $requiredMinutes,
                    'message' => "This time is available for {$availableMinutes} minutes, " . "but your selected services require {$requiredMinutes} minutes.",
                    'recommendations' => $recommendations,
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | No free time
            |--------------------------------------------------------------------------
            */

            $slots[] = [
                'start' => $slotStart->format('H:i'),
                'label' => $slotStart->format('g:i A') . ' - ' . $slotBlockEnd->format('g:i A'),
                'status' => 'booked',
                'available_minutes' => 0,
                'required_minutes' => $requiredMinutes,
                'message' => 'This time is not available.',
                'recommendations' => [],
            ];
        }

        return response()->json($slots);
    }

    /*
    |--------------------------------------------------------------------------
    | Store appointment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],

            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],

            'level' => ['required', 'in:gentle,mild,hard'],

            'add_on_id' => ['nullable', 'integer', 'exists:add_ons,id'],

            'has_previous_operations' => ['required', 'in:yes,no'],

            'body_problem' => ['nullable', 'string', 'max:2000'],

            'appointment_date' => ['required', 'date_format:Y-m-d'],

            'appointment_time' => ['required', 'date_format:H:i'],

            'payment_method' => ['required', 'in:branch,gcash'],

            'payment_type' => ['nullable', 'required_if:payment_method,gcash', 'in:full,downpayment'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        $service = Services::where('id', $validated['service_id'])->where('status', 'active')->first();

        if (!$service) {
            throw ValidationException::withMessages([
                'service_id' => 'Selected service is not available.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Therapist
        |--------------------------------------------------------------------------
        */

        $therapist = Therapists::where('id', $validated['therapist_id'])->where('status', 'available')->first();

        if (!$therapist) {
            throw ValidationException::withMessages([
                'therapist_id' => 'Selected therapist is not available.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Add-on
        |--------------------------------------------------------------------------
        */

        $addOn = null;

        if (!empty($validated['add_on_id'])) {
            $addOn = AddOns::where('id', $validated['add_on_id'])->where('status', 'active')->first();

            if (!$addOn) {
                throw ValidationException::withMessages([
                    'add_on_id' => 'Selected add-on is not available.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment date
        |--------------------------------------------------------------------------
        */

        $appointmentDate = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], $this->timezone)->startOfDay();

        $today = $this->manilaNow()->startOfDay();

        if ($appointmentDate->lt($today)) {
            throw ValidationException::withMessages([
                'appointment_date' => 'Appointment date cannot be in the past.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment start
        |--------------------------------------------------------------------------
        */

        $start = $this->appointmentDateTime($validated['appointment_date'], $validated['appointment_time']);

        /*
        |--------------------------------------------------------------------------
        | Booking window
        |--------------------------------------------------------------------------
        */

        $window = $this->getBookingWindow($validated['appointment_date']);

        if ($start->lt($window['opening']) || $start->gte($window['closing'])) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Selected appointment time is outside the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Don't allow past time
        |--------------------------------------------------------------------------
        */

        if ($start->lte($this->manilaNow())) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Selected appointment time has already passed.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) $service->duration_minutes;

        $addOnDuration = $addOn ? (int) $addOn->duration_minutes : 0;

        $totalDuration = $serviceDuration + $addOnDuration;

        /*
        |--------------------------------------------------------------------------
        | Appointment end
        |--------------------------------------------------------------------------
        */

        $end = $start->copy()->addMinutes($totalDuration);

        /*
        |--------------------------------------------------------------------------
        | Cannot go beyond 1:00 AM
        |--------------------------------------------------------------------------
        */

        if ($end->gt($window['closing'])) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected service duration extends beyond the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL SERVER-SIDE CONFLICT CHECK
        |--------------------------------------------------------------------------
        |
        | Never rely only on the frontend availability check.
        |
        */

        $existingAppointments = $this->getTherapistAppointments((int) $therapist->id, $validated['appointment_date']);

        foreach ($existingAppointments as $existingAppointment) {
            $range = $this->getAppointmentRange($existingAppointment);

            if ($this->appointmentOverlaps($start, $end, $range['start'], $range['end'])) {
                throw ValidationException::withMessages([
                    'appointment_time' => 'The selected time is no longer available. ' . 'Please choose another schedule.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Prices
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Prices are taken from the database.
        | We do NOT trust prices sent from the browser.
        |
        */

        $servicePrice = (float) $service->price;

        $addOnPrice = $addOn ? (float) $addOn->price : 0;

        $totalAmount = $servicePrice + $addOnPrice;

        /*
        |--------------------------------------------------------------------------
        | Payment amount
        |--------------------------------------------------------------------------
        |
        | Branch:
        |   payment_type = null
        |   amount_paid = 0
        |
        | GCash Full:
        |   payment_type = full
        |   amount_paid = 100%
        |
        | GCash Downpayment:
        |   payment_type = downpayment
        |   amount_paid = 50%
        |
        */

        $paymentType = $validated['payment_type'] ?? null;

        if ($validated['payment_method'] === 'branch') {
            $paymentType = null;

            $amountPaid = 0;
        } elseif ($paymentType === 'downpayment') {
            $amountPaid = round($totalAmount * 0.5, 2);
        } else {
            // GCash full payment
            $amountPaid = $totalAmount;
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment status
        |--------------------------------------------------------------------------
        |
        | Every newly submitted appointment starts as pending.
        |
        */

        $status = 'pending';

        /*
        |--------------------------------------------------------------------------
        | Create appointment
        |--------------------------------------------------------------------------
        */

        $appointment = UsersAppointments::create([
            'user_id' => Auth::id(),

            'service_id' => $service->id,

            'therapist_id' => $therapist->id,

            'service_price' => $servicePrice,

            'service_duration_minutes' => $serviceDuration,

            'level' => $validated['level'],

            'add_on_id' => $addOn?->id,

            'addons_price' => $addOnPrice,

            'addons_duration_minutes' => $addOnDuration,

            'has_previous_operations' => $validated['has_previous_operations'],

            'body_problem' => $validated['body_problem'] ?? null,

            'appointment_date' => $validated['appointment_date'],

            'appointment_time' => $validated['appointment_time'],

            'appointment_end_time' => $end->format('H:i'),

            'payment_method' => $validated['payment_method'],

            'payment_type' => $paymentType,

            'amount_paid' => $amountPaid,

            'status' => $status,
        ]);

        /*
        |--------------------------------------------------------------------------
        | GCash
        |--------------------------------------------------------------------------
        |
        | PayMongo integration will go here.
        |
        */

        if ($validated['payment_method'] === 'gcash') {
            /*
            |--------------------------------------------------------------------------
            | TODO:
            |
            | 1. Create PayMongo checkout session
            | 2. Save PayMongo checkout/payment reference
            | 3. Redirect user to PayMongo
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('user.appointment')
                ->with('success', 'Appointment created successfully. ' . 'Please complete your GCash payment.');
        }

        /*
        |--------------------------------------------------------------------------
        | Pay at branch
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('user.appointment')
            ->with('success', 'Appointment submitted successfully. ' . 'Please wait for confirmation.');
    }
}
