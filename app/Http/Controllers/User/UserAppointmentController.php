<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\AddOns;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use App\Services\PayMongoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserAppointmentController extends Controller
{
    private string $timezone = 'Asia/Manila';
    private int $openingHour = 13; // 1:00 PM
    private int $closingHour = 1; // 1:00 AM next day
    private int $slotInterval = 30;

    public function __construct(private PayMongoService $payMongo) {}

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
    | Cancelled, failed and rejected appointments do not block the therapist.
    |
    */

    private function getTherapistAppointments(int $therapistId, string $date)
    {
        return UsersAppointments::where('therapist_id', $therapistId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])
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

        // Get the service currently selected by the user.
        $currentService = Services::where('status', 'active')->find($currentServiceId);

        if (!$currentService) {
            return [];
        }

        // Available time for the massage itself.
        $availableServiceMinutes = $availableMinutes - $addOnDuration;

        if ($availableServiceMinutes <= 0) {
            return [];
        }

        return Services::where('status', 'active')
            ->where('name', $currentService->name)
            ->where('duration_minutes', '<', $currentService->duration_minutes)
            ->where('duration_minutes', '<=', $availableServiceMinutes)
            ->orderByDesc('duration_minutes')
            ->get()
            ->map(function ($service) use ($addOnDuration, $addOnPrice) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
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
        | Prices
        |--------------------------------------------------------------------------
        |
        | Prices always come from the database.
        |
        */

        $servicePrice = (float) $service->price;

        $addOnPrice = $addOn ? (float) $addOn->price : 0;

        $totalAmount = $servicePrice + $addOnPrice;

        /*
        |--------------------------------------------------------------------------
        | Payment amount
        |--------------------------------------------------------------------------
        */

        $paymentType = $validated['payment_type'] ?? null;

        if ($validated['payment_method'] === 'branch') {
            $paymentType = null;
            $paymentAmount = 0;
        } elseif ($paymentType === 'downpayment') {
            $paymentAmount = round($totalAmount * 0.5, 2);
        } else {
            // GCash full payment
            $paymentAmount = $totalAmount;
        }

        /*
        |--------------------------------------------------------------------------
        | Initial payment state
        |--------------------------------------------------------------------------
        */

        $amountPaid = 0;

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        |
        | Start database transaction.
        |
        | We lock the therapist row so that two users cannot
        | simultaneously book the same therapist/time.
        |--------------------------------------------------------------------------
        */

        $appointment = DB::transaction(function () use ($validated, $service, $therapist, $addOn, $servicePrice, $addOnPrice, $serviceDuration, $addOnDuration, $paymentType, $paymentAmount, $amountPaid, $start, $end) {
            /*
            |--------------------------------------------------------------------------
            | LOCK THERAPIST
            |--------------------------------------------------------------------------
            |
            | This is the important concurrency protection.
            |
            | If another request is currently booking this therapist,
            | that request must finish before this one can continue.
            |
            */

            $lockedTherapist = Therapists::where('id', $therapist->id)->where('status', 'available')->lockForUpdate()->first();

            if (!$lockedTherapist) {
                throw ValidationException::withMessages([
                    'therapist_id' => 'Selected therapist is no longer available.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL SERVER-SIDE CONFLICT CHECK
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | This check happens AFTER locking the therapist.
            |
            | This prevents two simultaneous requests from both
            | passing the availability check.
            |
            */

            $existingAppointments = $this->getTherapistAppointments((int) $lockedTherapist->id, $validated['appointment_date']);

            foreach ($existingAppointments as $existingAppointment) {
                $range = $this->getAppointmentRange($existingAppointment);

                if ($this->appointmentOverlaps($start, $end, $range['start'], $range['end'])) {
                    throw ValidationException::withMessages([
                        'appointment_time' => 'The selected time is no longer available. Please choose another schedule.',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Create appointment
            |--------------------------------------------------------------------------
            */

            return UsersAppointments::create([
                'user_id' => Auth::id(),

                'service_id' => $service->id,
                'therapist_id' => $lockedTherapist->id,

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

                'payment_amount' => $paymentAmount,

                'payment_status' => 'pending',

                'status' => AppointmentStatus::PENDING,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | GCash
        |--------------------------------------------------------------------------
        */

        if ($validated['payment_method'] === 'gcash') {
            $referenceNumber = 'APPT-' . $appointment->id . '-' . strtoupper(Str::random(6));

            /*
            |--------------------------------------------------------------------------
            | Create PayMongo Checkout Session
            |--------------------------------------------------------------------------
            */

            $checkout = $this->payMongo->createCheckoutSession(
                referenceNumber: $referenceNumber,

                description: 'Padayon Massage Center Appointment ' . $referenceNumber,

                amount: (int) round($paymentAmount * 100),

                successUrl: route('user.appointments.payment.success', $appointment),

                cancelUrl: route('user.appointments.payment.cancel', $appointment),

                customerName: Auth::user()->name,

                customerEmail: Auth::user()->email,
            );

            $checkoutSession = $checkout['data'];

            /*
            |--------------------------------------------------------------------------
            | Save PayMongo information
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'paymongo_checkout_session_id' => $checkoutSession['id'],

                'paymongo_reference_number' => $referenceNumber,

                /*
                | Your scheduler currently expires pending
                | payments after 1 minute.
                */
                'paymongo_checkout_expires_at' => now('Asia/Manila')->addMinute(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Redirect to PayMongo
            |--------------------------------------------------------------------------
            */

            $checkoutUrl = $checkoutSession['attributes']['checkout_url'];

            return redirect()->away($checkoutUrl);
        }

        /*
        |--------------------------------------------------------------------------
        | Pay at branch
        |--------------------------------------------------------------------------
        */

        return redirect()->route('user.my-appointments')->with('success', 'Appointment submitted successfully. Please wait for confirmation.');
    }

    public function paymentSuccess(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        return redirect()->route('user.my-appointments')->with('success', 'Payment submitted successfully. Your payment is being verified.');
    }

    public function paymentCancel(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        Log::info('PayMongo cancel URL reached.', [
            'appointment_id' => $appointment->id,
            'payment_status' => $appointment->payment_status,
            'paymongo_checkout_session_id' => $appointment->paymongo_checkout_session_id,
        ]);

        return redirect()->route('user.my-appointments')->with('error', 'Payment was cancelled. No payment was completed.');
    }

    public function payAgain(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        /*
    |--------------------------------------------------------------------------
    | Only GCash appointments can be paid again
    |--------------------------------------------------------------------------
    */
        if ($appointment->payment_method !== 'gcash') {
            return back()->with('error', 'This appointment does not use GCash payment.');
        }

        /*
    |--------------------------------------------------------------------------
    | Only failed or pending payments can be retried
    |--------------------------------------------------------------------------
    */
        if (!in_array($appointment->payment_status, ['failed', 'pending'])) {
            return back()->with('error', 'This payment cannot be retried.');
        }

        /*
    |--------------------------------------------------------------------------
    | Appointment must still be usable
    |--------------------------------------------------------------------------
    */
        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::FAILED])) {
            return back()->with('error', 'This appointment is not available for payment retry.');
        }

        /*
    |--------------------------------------------------------------------------
    | Create NEW PayMongo Checkout Session
    |--------------------------------------------------------------------------
    */
        $referenceNumber = 'APPT-' . $appointment->id . '-' . strtoupper(Str::random(6));

        $checkout = $this->payMongo->createCheckoutSession(referenceNumber: $referenceNumber, description: 'Padayon Massage Center Appointment ' . $referenceNumber, amount: (int) round($appointment->payment_amount * 100), successUrl: route('user.appointments.payment.success', $appointment), cancelUrl: route('user.appointments.payment.cancel', $appointment), customerName: Auth::user()->name, customerEmail: Auth::user()->email);

        $checkoutSession = $checkout['data'];

        /*
    |--------------------------------------------------------------------------
    | Reset payment attempt
    |--------------------------------------------------------------------------
    */
        $appointment->update([
            'paymongo_checkout_session_id' => $checkoutSession['id'],

            'paymongo_reference_number' => $referenceNumber,

            'paymongo_payment_id' => null,

            'paymongo_checkout_expires_at' => now('Asia/Manila')->addMinutes(30),

            'payment_status' => 'pending',

            'amount_paid' => 0,

            'paid_at' => null,

            'status' => AppointmentStatus::PENDING,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Redirect to NEW Checkout Session
    |--------------------------------------------------------------------------
    */
        $checkoutUrl = $checkoutSession['attributes']['checkout_url'];

        return redirect()->away($checkoutUrl);
    }
}
