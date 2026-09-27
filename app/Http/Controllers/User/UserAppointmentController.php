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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserAppointmentController extends Controller
{
    private string $timezone = 'Asia/Manila';

    /**
     * Main operating hours:
     *
     * 12:00 AM - 1:00 AM
     * 1:00 PM  - 12:00 AM
     *
     * The 12:00 AM - 1:00 AM period is represented
     * as part of the selected calendar date.
     */
    private int $openingHour = 13;

    private int $closingHour = 1;

    private int $slotInterval = 30;

    public function __construct(private PayMongoService $payMongo) {}

    /*
    |--------------------------------------------------------------------------
    | Appointment Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $services = Services::query()->where('status', 'active')->orderBy('name')->get();

        $therapists = Therapists::query()->where('status', 'available')->orderBy('name')->get();

        $addOns = AddOns::query()->where('status', 'active')->orderBy('name')->get();

        return view('user.appointment', compact('services', 'therapists', 'addOns'));
    }

    /*
    |--------------------------------------------------------------------------
    | Manila Current Time
    |--------------------------------------------------------------------------
    */

    private function manilaNow(): Carbon
    {
        return Carbon::now($this->timezone);
    }

    /*
    |--------------------------------------------------------------------------
    | Booking Windows
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | September 30
    |
    | 12:00 AM - 1:00 AM
    |
    | CLOSED
    |
    | 1:00 PM - 12:00 AM
    |
    |--------------------------------------------------------------------------
    */

    private function getBookingWindows(string $date): array
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        return [
            [
                /*
                |--------------------------------------------------------------------------
                | First operating window
                |--------------------------------------------------------------------------
                |
                | September 30, 12:00 AM
                | ->
                | September 30, 1:00 AM
                |
                */
                'opening' => $bookingDate->copy()->setTime(0, 0, 0),

                'closing' => $bookingDate->copy()->setTime(1, 0, 0),
            ],

            [
                /*
                |--------------------------------------------------------------------------
                | Second operating window
                |--------------------------------------------------------------------------
                |
                | September 30, 1:00 PM
                | ->
                | October 1, 12:00 AM
                |
                */
                'opening' => $bookingDate->copy()->setTime(13, 0, 0),

                'closing' => $bookingDate->copy()->addDay()->setTime(0, 0, 0),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Build Appointment DateTime
    |--------------------------------------------------------------------------
    */

    private function appointmentDateTime(string $date, string $time): Carbon
    {
        $bookingDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return $bookingDate->copy()->setTime($hour, $minute, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Therapist Appointments
    |--------------------------------------------------------------------------
    |
    | We check:
    |
    | 1. Appointments starting on the selected date.
    | 2. Appointments starting on the previous date.
    |
    | Why?
    |
    | Example:
    |
    | September 30
    | 11:30 PM - 12:30 AM
    |
    | When October 1 availability is requested,
    | this appointment must still block:
    |
    | October 1
    | 12:00 AM - 12:30 AM
    |
    |--------------------------------------------------------------------------
    */

    private function getTherapistAppointments(int $therapistId, string $date)
    {
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date, $this->timezone)->startOfDay();

        $previousDate = $selectedDate->copy()->subDay()->format('Y-m-d');

        $selectedDateString = $selectedDate->format('Y-m-d');

        return UsersAppointments::query()
            ->where('therapist_id', $therapistId)

            /*
            |--------------------------------------------------------------------------
            | Include selected date AND previous date
            |--------------------------------------------------------------------------
            */
            ->whereIn('appointment_date', [$previousDate, $selectedDateString])

            /*
            |--------------------------------------------------------------------------
            | These statuses do NOT block the therapist
            |--------------------------------------------------------------------------
            */
            ->whereNotIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::REJECTED->value, AppointmentStatus::FAILED->value, AppointmentStatus::NO_SHOW->value])

            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Existing Appointment DateTime Range
    |--------------------------------------------------------------------------
    */

    private function getAppointmentRange(UsersAppointments $appointment): array
    {
        $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

        $start = $this->appointmentDateTime($appointmentDate, $appointment->appointment_time);

        $end = $this->appointmentDateTime($appointmentDate, $appointment->appointment_end_time);

        /*
        |--------------------------------------------------------------------------
        | Appointment crosses midnight
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | start = September 30 11:30 PM
        | end   = September 30 12:30 AM
        |
        | Since end <= start, move end to October 1.
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
    | Appointment Overlap
    |--------------------------------------------------------------------------
    */

    private function appointmentOverlaps(Carbon $start, Carbon $end, Carbon $existingStart, Carbon $existingEnd): bool
    {
        return $start->lt($existingEnd) && $end->gt($existingStart);
    }

    /*
    |--------------------------------------------------------------------------
    | Find Next Booking
    |--------------------------------------------------------------------------
    |
    | Finds the next appointment that starts after the current slot.
    |
    | IMPORTANT:
    | The $closing parameter prevents the free period from crossing
    | into another operating window.
    |--------------------------------------------------------------------------
    */

    private function getNextBookingStart(Carbon $slotStart, array $appointments, Carbon $closing): Carbon
    {
        $nextBooking = $closing->copy();

        foreach ($appointments as $appointment) {
            $range = $this->getAppointmentRange($appointment);

            /*
            |--------------------------------------------------------------------------
            | Appointment already ended before this slot
            |--------------------------------------------------------------------------
            */
            if ($range['end']->lte($slotStart)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Appointment starts after this slot
            |--------------------------------------------------------------------------
            */
            if ($range['start']->gt($slotStart) && $range['start']->lt($nextBooking)) {
                $nextBooking = $range['start']->copy();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Never exceed this operating window
        |--------------------------------------------------------------------------
        */
        if ($nextBooking->gt($closing)) {
            $nextBooking = $closing->copy();
        }

        return $nextBooking;
    }

    /*
    |--------------------------------------------------------------------------
    | Recommended Shorter Services
    |--------------------------------------------------------------------------
    */

    private function getRecommendedServices(int $availableMinutes, ?AddOns $selectedAddOn, int $currentServiceId): array
    {
        $addOnDuration = $selectedAddOn ? (int) $selectedAddOn->duration_minutes : 0;

        $addOnPrice = $selectedAddOn ? (float) $selectedAddOn->price : 0;

        $currentService = Services::query()->where('status', 'active')->find($currentServiceId);

        if (!$currentService) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Available minutes for the service itself
        |--------------------------------------------------------------------------
        */
        $availableServiceMinutes = $availableMinutes - $addOnDuration;

        if ($availableServiceMinutes <= 0) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Same service name, shorter duration
        |--------------------------------------------------------------------------
        */
        return Services::query()
            ->where('status', 'active')
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
    | Validate Requested Start Time
    |--------------------------------------------------------------------------
    */

    private function validateStartTime(string $date, string $time): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        /*
        |--------------------------------------------------------------------------
        | Strict 30-minute interval
        |--------------------------------------------------------------------------
        */
        if ($minute !== 0 && $minute !== 30) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Appointments can only start on a 30-minute interval.',
            ]);
        }

        $start = $this->appointmentDateTime($date, $time);

        $windows = $this->getBookingWindows($date);

        $insideBookingWindow = false;

        foreach ($windows as $window) {
            if ($start->gte($window['opening']) && $start->lt($window['closing'])) {
                $insideBookingWindow = true;
                break;
            }
        }

        if (!$insideBookingWindow) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Selected appointment time is outside the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Do not allow past appointments
        |--------------------------------------------------------------------------
        */
        if ($start->lte($this->manilaNow())) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Selected appointment time has already passed.',
            ]);
        }

        return $start;
    }

    /*
    |--------------------------------------------------------------------------
    | Check Selected Appointment Availability
    |--------------------------------------------------------------------------
    |
    | $ignoreAppointmentId is used by payAgain().
    |
    | Without it, the appointment could conflict with itself.
    |--------------------------------------------------------------------------
    */

    private function ensureAppointmentAvailable(int $therapistId, string $date, Carbon $start, Carbon $end, ?int $ignoreAppointmentId = null): void
    {
        $appointments = $this->getTherapistAppointments($therapistId, $date);

        foreach ($appointments as $existingAppointment) {
            /*
            |--------------------------------------------------------------------------
            | Ignore current appointment during payment retry
            |--------------------------------------------------------------------------
            */
            if ($ignoreAppointmentId !== null && (int) $existingAppointment->id === $ignoreAppointmentId) {
                continue;
            }

            $range = $this->getAppointmentRange($existingAppointment);

            if ($this->appointmentOverlaps($start, $end, $range['start'], $range['end'])) {
                throw ValidationException::withMessages([
                    'appointment_time' => 'The selected time is no longer available. Please choose another schedule.',
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Available Slots
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
        | Service
        |--------------------------------------------------------------------------
        */

        $service = Services::query()->where('id', $validated['service_id'])->where('status', 'active')->first();

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
        | Therapist
        |--------------------------------------------------------------------------
        */

        $therapist = Therapists::query()->where('id', $validated['therapist_id'])->where('status', 'available')->first();

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
        | Add-on
        |--------------------------------------------------------------------------
        */

        $addOn = null;

        if (!empty($validated['add_on_id'])) {
            $addOn = AddOns::query()->where('id', $validated['add_on_id'])->where('status', 'active')->first();

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
        | Selected Date
        |--------------------------------------------------------------------------
        */

        $selectedDate = Carbon::createFromFormat('Y-m-d', $validated['date'], $this->timezone)->startOfDay();

        $today = $this->manilaNow()->startOfDay();

        if ($selectedDate->lt($today)) {
            return response()->json(
                [
                    'message' => 'Appointment date cannot be in the past.',
                ],
                422,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) $service->duration_minutes;

        $addOnDuration = $addOn ? (int) $addOn->duration_minutes : 0;

        $requiredMinutes = $serviceDuration + $addOnDuration;

        if ($requiredMinutes <= 0) {
            return response()->json(
                [
                    'message' => 'The selected service has an invalid duration.',
                ],
                422,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Booking Windows
        |--------------------------------------------------------------------------
        */

        $windows = $this->getBookingWindows($validated['date']);

        /*
        |--------------------------------------------------------------------------
        | Existing Therapist Appointments
        |--------------------------------------------------------------------------
        */

        $appointments = $this->getTherapistAppointments((int) $validated['therapist_id'], $validated['date']);

        /*
        |--------------------------------------------------------------------------
        | Current Manila Time
        |--------------------------------------------------------------------------
        */

        $now = $this->manilaNow();

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        |
        | Only skip past slots when the selected date is TODAY.
        |
        | Future dates must show their complete schedule.
        |--------------------------------------------------------------------------
        */

        $isToday = $selectedDate->isSameDay($now);

        $slots = [];

        /*
        |--------------------------------------------------------------------------
        | Generate Both Operating Windows
        |--------------------------------------------------------------------------
        */

        foreach ($windows as $window) {
            $opening = $window['opening']->copy();
            $closing = $window['closing']->copy();

            for ($slotStart = $opening->copy(); $slotStart->lt($closing); $slotStart->addMinutes($this->slotInterval)) {
                /*
                |--------------------------------------------------------------------------
                | Skip past slots ONLY for today
                |--------------------------------------------------------------------------
                */

                if ($isToday && $slotStart->lte($now)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 30-minute display block
                |--------------------------------------------------------------------------
                */

                $slotBlockEnd = $slotStart->copy()->addMinutes($this->slotInterval);

                /*
                |--------------------------------------------------------------------------
                | Never allow display block outside window
                |--------------------------------------------------------------------------
                |
                | 12:30 AM -> 1:00 AM = valid
                |
                | 12:30 AM -> 1:30 AM = invalid
                |--------------------------------------------------------------------------
                */

                if ($slotBlockEnd->gt($closing)) {
                    $slotBlockEnd = $closing->copy();
                }

                /*
                |--------------------------------------------------------------------------
                | Check whether this block is occupied
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
                | Date Label
                |--------------------------------------------------------------------------
                */

                $dateLabel = $slotStart->format('F j, Y');

                /*
                |--------------------------------------------------------------------------
                | BOOKED
                |--------------------------------------------------------------------------
                */

                if ($isBooked) {
                    $slots[] = [
                        'start' => $slotStart->format('H:i'),

                        'end' => $slotBlockEnd->format('H:i'),

                        'label' => $slotStart->format('g:i A') . ' - ' . $slotBlockEnd->format('g:i A'),

                        'date_label' => $dateLabel,

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
                | Find Next Booking
                |--------------------------------------------------------------------------
                */

                $nextBookingStart = $this->getNextBookingStart($slotStart, $appointments->all(), $closing);

                /*
                |--------------------------------------------------------------------------
                | Continuous Free Period
                |--------------------------------------------------------------------------
                */

                $availableMinutes = $slotStart->diffInMinutes($nextBookingStart);

                /*
                |--------------------------------------------------------------------------
                | Full Service Fits
                |--------------------------------------------------------------------------
                */

                if ($availableMinutes >= $requiredMinutes) {
                    $slotEnd = $slotStart->copy()->addMinutes($requiredMinutes);

                    /*
                    |--------------------------------------------------------------------------
                    | Extra safety check
                    |--------------------------------------------------------------------------
                    */

                    if ($slotEnd->lte($closing)) {
                        $slots[] = [
                            'start' => $slotStart->format('H:i'),

                            'end' => $slotEnd->format('H:i'),

                            'label' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),

                            'date_label' => $dateLabel,

                            'status' => 'available',

                            'available_minutes' => $availableMinutes,

                            'required_minutes' => $requiredMinutes,

                            'message' => null,

                            'recommendations' => [],
                        ];

                        continue;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Some Free Time Exists But Not Enough
                |--------------------------------------------------------------------------
                */

                if ($availableMinutes > 0) {
                    $recommendations = $this->getRecommendedServices($availableMinutes, $addOn, (int) $service->id);

                    $freeEnd = $slotStart->copy()->addMinutes($availableMinutes);

                    /*
                    |--------------------------------------------------------------------------
                    | Never exceed operating window
                    |--------------------------------------------------------------------------
                    */

                    if ($freeEnd->gt($closing)) {
                        $freeEnd = $closing->copy();
                    }

                    $actualAvailableMinutes = $slotStart->diffInMinutes($freeEnd);

                    $slots[] = [
                        'start' => $slotStart->format('H:i'),

                        'end' => $freeEnd->format('H:i'),

                        'label' => $slotStart->format('g:i A') . ' - ' . $freeEnd->format('g:i A'),

                        'date_label' => $dateLabel,

                        'status' => 'adjust_service',

                        'available_minutes' => $actualAvailableMinutes,

                        'required_minutes' => $requiredMinutes,

                        'message' => sprintf('This time is available for %d minutes, but your selected services require %d minutes.', $actualAvailableMinutes, $requiredMinutes),

                        'recommendations' => $recommendations,
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | No Free Time
                |--------------------------------------------------------------------------
                */

                $slots[] = [
                    'start' => $slotStart->format('H:i'),

                    'end' => $slotBlockEnd->format('H:i'),

                    'label' => $slotStart->format('g:i A') . ' - ' . $slotBlockEnd->format('g:i A'),

                    'date_label' => $dateLabel,

                    'status' => 'booked',

                    'available_minutes' => 0,

                    'required_minutes' => $requiredMinutes,

                    'message' => 'This time is not available.',

                    'recommendations' => [],
                ];
            }
        }

        return response()->json($slots);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Appointment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
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
        | Date
        |--------------------------------------------------------------------------
        */

        $appointmentDate = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], $this->timezone)->startOfDay();

        if ($appointmentDate->lt($this->manilaNow()->startOfDay())) {
            throw ValidationException::withMessages([
                'appointment_date' => 'Appointment date cannot be in the past.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        $service = Services::query()->where('id', $validated['service_id'])->where('status', 'active')->first();

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

        $therapist = Therapists::query()->where('id', $validated['therapist_id'])->where('status', 'available')->first();

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
            $addOn = AddOns::query()->where('id', $validated['add_on_id'])->where('status', 'active')->first();

            if (!$addOn) {
                throw ValidationException::withMessages([
                    'add_on_id' => 'Selected add-on is not available.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Start Time
        |--------------------------------------------------------------------------
        */

        $start = $this->validateStartTime($validated['appointment_date'], $validated['appointment_time']);

        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) $service->duration_minutes;

        $addOnDuration = $addOn ? (int) $addOn->duration_minutes : 0;

        $totalDuration = $serviceDuration + $addOnDuration;

        if ($totalDuration <= 0) {
            throw ValidationException::withMessages([
                'service_id' => 'The selected service has an invalid duration.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | End Time
        |--------------------------------------------------------------------------
        */

        $end = $start->copy()->addMinutes($totalDuration);

        /*
        |--------------------------------------------------------------------------
        | Booking Window
        |--------------------------------------------------------------------------
        |
        | A service must fit entirely inside ONE operating window.
        |
        | It cannot cross:
        |
        | 1:00 AM
        |
        | or
        |
        | 1:00 PM
        |
        |--------------------------------------------------------------------------
        */

        $windows = $this->getBookingWindows($validated['appointment_date']);

        $fitsBookingWindow = false;

        foreach ($windows as $window) {
            if ($start->gte($window['opening']) && $end->lte($window['closing'])) {
                $fitsBookingWindow = true;
                break;
            }
        }

        if (!$fitsBookingWindow) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected service duration extends beyond the booking hours.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Database Prices
        |--------------------------------------------------------------------------
        */

        $servicePrice = (float) $service->price;

        $addOnPrice = $addOn ? (float) $addOn->price : 0;

        $totalAmount = round($servicePrice + $addOnPrice, 2);

        /*
        |--------------------------------------------------------------------------
        | Payment Amount
        |--------------------------------------------------------------------------
        */

        $paymentType = $validated['payment_type'] ?? null;

        if ($validated['payment_method'] === 'branch') {
            $paymentType = null;
            $paymentAmount = 0.0;
        } elseif ($paymentType === 'downpayment') {
            $paymentAmount = round($totalAmount * 0.5, 2);
        } else {
            $paymentAmount = $totalAmount;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Appointment
        |--------------------------------------------------------------------------
        |
        | Lock therapist row.
        |
        | Then perform final conflict check.
        |--------------------------------------------------------------------------
        */

        $appointment = DB::transaction(function () use ($validated, $therapist, $service, $addOn, $servicePrice, $addOnPrice, $serviceDuration, $addOnDuration, $paymentType, $paymentAmount, $start, $end) {
            /*
                |--------------------------------------------------------------------------
                | Lock Therapist
                |--------------------------------------------------------------------------
                */

            $lockedTherapist = Therapists::query()->where('id', $therapist->id)->where('status', 'available')->lockForUpdate()->first();

            if (!$lockedTherapist) {
                throw ValidationException::withMessages([
                    'therapist_id' => 'Selected therapist is no longer available.',
                ]);
            }

            /*
                |--------------------------------------------------------------------------
                | Final Conflict Check
                |--------------------------------------------------------------------------
                */

            $this->ensureAppointmentAvailable((int) $lockedTherapist->id, $validated['appointment_date'], $start, $end);

            /*
                |--------------------------------------------------------------------------
                | Create Appointment
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

                /*
                    |--------------------------------------------------------------------------
                    | Webhook updates this after successful PayMongo payment
                    |--------------------------------------------------------------------------
                    */

                'amount_paid' => 0,

                'payment_amount' => $paymentAmount,

                'payment_status' => 'pending',

                /*
                    |--------------------------------------------------------------------------
                    | Appointment remains pending until:
                    |
                    | Branch:
                    | Admin confirms it.
                    |
                    | GCash:
                    | PayMongo webhook confirms payment.
                    |--------------------------------------------------------------------------
                    */

                'status' => AppointmentStatus::PENDING,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Branch Payment
        |--------------------------------------------------------------------------
        */

        if ($validated['payment_method'] === 'branch') {
            return redirect()->route('user.my-appointments')->with('success', 'Appointment submitted successfully. Please wait for confirmation.');
        }

        /*
        |--------------------------------------------------------------------------
        | GCash / PayMongo
        |--------------------------------------------------------------------------
        */

        try {
            $referenceNumber = 'APPT-' . $appointment->id . '-' . strtoupper(Str::random(6));

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

            $checkoutUrl = $checkoutSession['attributes']['checkout_url'];

            /*
            |--------------------------------------------------------------------------
            | Save PayMongo Information
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'paymongo_checkout_session_id' => $checkoutSession['id'],

                'paymongo_reference_number' => $referenceNumber,

                /*
                |--------------------------------------------------------------------------
                | Keep this longer than one minute.
                |--------------------------------------------------------------------------
                */

                'paymongo_checkout_expires_at' => $this->manilaNow()->addMinutes(30),

                'payment_status' => 'pending',
            ]);

            return redirect()->away($checkoutUrl);
        } catch (Throwable $e) {
            /*
            |--------------------------------------------------------------------------
            | PayMongo failed AFTER appointment creation
            |--------------------------------------------------------------------------
            |
            | Release therapist slot by marking appointment FAILED.
            |--------------------------------------------------------------------------
            */

            Log::error('PayMongo checkout creation failed.', [
                'appointment_id' => $appointment->id,

                'user_id' => Auth::id(),

                'error' => $e->getMessage(),
            ]);

            $appointment->update([
                'payment_status' => 'failed',

                'status' => AppointmentStatus::FAILED,
            ]);

            return redirect()->route('user.appointment')->with('error', 'We could not start the GCash payment. Your appointment was not reserved. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PayMongo Success URL
    |--------------------------------------------------------------------------
    |
    | DO NOT confirm payment here.
    |
    | PayMongo webhook is responsible for confirmation.
    |--------------------------------------------------------------------------
    */

    public function paymentSuccess(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        return redirect()->route('user.my-appointments')->with('success', 'You returned from PayMongo. Your payment is being verified.');
    }

    /*
    |--------------------------------------------------------------------------
    | PayMongo Cancel URL
    |--------------------------------------------------------------------------
    */

    public function paymentCancel(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        Log::info('PayMongo cancel URL reached.', [
            'appointment_id' => $appointment->id,

            'payment_status' => $appointment->payment_status,

            'appointment_status' => $appointment->status,

            'paymongo_checkout_session_id' => $appointment->paymongo_checkout_session_id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Release appointment
        |--------------------------------------------------------------------------
        |
        | A cancelled checkout must not continue blocking the therapist.
        |--------------------------------------------------------------------------
        */

        if ($appointment->payment_method === 'gcash' && $appointment->payment_status === 'pending' && $appointment->status === AppointmentStatus::PENDING) {
            $appointment->update([
                'payment_status' => 'failed',

                'status' => AppointmentStatus::FAILED,
            ]);
        }

        return redirect()->route('user.my-appointments')->with('error', 'Payment was cancelled. The appointment payment attempt was not completed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Pay Again
    |--------------------------------------------------------------------------
    */

    public function payAgain(UsersAppointments $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        /*
        |--------------------------------------------------------------------------
        | Only GCash
        |--------------------------------------------------------------------------
        */

        if ($appointment->payment_method !== 'gcash') {
            return back()->with('error', 'This appointment does not use GCash payment.');
        }

        /*
        |--------------------------------------------------------------------------
        | Only Pending / Failed Payments
        |--------------------------------------------------------------------------
        */

        if (!in_array($appointment->payment_status, ['failed', 'pending'], true)) {
            return back()->with('error', 'This payment cannot be retried.');
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment Status
        |--------------------------------------------------------------------------
        */

        if (!in_array($appointment->status, [AppointmentStatus::PENDING, AppointmentStatus::FAILED], true)) {
            return back()->with('error', 'This appointment is not available for payment retry.');
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment Must Not Already Be In The Past
        |--------------------------------------------------------------------------
        */

        try {
            $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

            $start = $this->appointmentDateTime($appointmentDate, $appointment->appointment_time);

            if ($start->lte($this->manilaNow())) {
                return back()->with('error', 'This appointment time has already passed.');
            }
        } catch (Throwable $e) {
            Log::warning('Could not validate appointment time during payment retry.', [
                'appointment_id' => $appointment->id,

                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'We could not validate the appointment schedule. Please try again.');
        }

        /*
        |--------------------------------------------------------------------------
        | Build Appointment Range
        |--------------------------------------------------------------------------
        */

        $appointmentDate = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

        $start = $this->appointmentDateTime($appointmentDate, $appointment->appointment_time);

        $end = $this->appointmentDateTime($appointmentDate, $appointment->appointment_end_time);

        /*
        |--------------------------------------------------------------------------
        | Handle Midnight
        |--------------------------------------------------------------------------
        */

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Therapist Still Available
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Ignore the current appointment itself.
        |
        | Otherwise it would conflict with itself.
        |--------------------------------------------------------------------------
        */

        try {
            DB::transaction(function () use ($appointment, $appointmentDate, $start, $end) {
                $therapist = Therapists::query()->where('id', $appointment->therapist_id)->where('status', 'available')->lockForUpdate()->first();

                if (!$therapist) {
                    throw ValidationException::withMessages([
                        'therapist_id' => 'The therapist is no longer available.',
                    ]);
                }

                $this->ensureAppointmentAvailable((int) $therapist->id, $appointmentDate, $start, $end, (int) $appointment->id);
            });
        } catch (ValidationException $e) {
            return back()->with('error', 'This appointment time is no longer available. Please contact the massage center.');
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Amount
        |--------------------------------------------------------------------------
        */

        $paymentAmount = (float) $appointment->payment_amount;

        if ($paymentAmount <= 0) {
            return back()->with('error', 'Invalid payment amount.');
        }

        /*
        |--------------------------------------------------------------------------
        | Create NEW PayMongo Checkout Session
        |--------------------------------------------------------------------------
        */

        try {
            $referenceNumber = 'APPT-' . $appointment->id . '-' . strtoupper(Str::random(6));

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
            | Reset Payment Attempt
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'paymongo_checkout_session_id' => $checkoutSession['id'],

                'paymongo_reference_number' => $referenceNumber,

                'paymongo_payment_id' => null,

                'paymongo_checkout_expires_at' => $this->manilaNow()->addMinutes(30),

                'payment_status' => 'pending',

                /*
                |--------------------------------------------------------------------------
                | Do not claim payment was received.
                |--------------------------------------------------------------------------
                */

                'amount_paid' => 0,

                'paid_at' => null,

                'status' => AppointmentStatus::PENDING,
            ]);

            $checkoutUrl = $checkoutSession['attributes']['checkout_url'];

            return redirect()->away($checkoutUrl);
        } catch (Throwable $e) {
            Log::error('PayMongo retry checkout creation failed.', [
                'appointment_id' => $appointment->id,

                'error' => $e->getMessage(),
            ]);

            $appointment->update([
                'payment_status' => 'failed',

                'status' => AppointmentStatus::FAILED,
            ]);

            return back()->with('error', 'We could not start the payment again. Please try again.');
        }
    }
}
