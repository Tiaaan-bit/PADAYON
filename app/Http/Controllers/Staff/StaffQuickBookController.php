<?php

namespace App\Http\Controllers\Staff;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Mail\WalkInRegistrationInvitation;
use App\Models\AddOns;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use App\Models\WalkInCustomer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StaffQuickBookController extends Controller
{
    private string $timezone = 'Asia/Manila';

    private int $openingHour = 13;

    private int $closingHour = 1;

    private int $slotInterval = 30;

    /**
     * Display Quick Book page.
     */
    public function index()
    {
        return view('staff.quick-book', [
            'services' => Services::query()->where('status', 'active')->orderBy('name')->get(),

            'therapists' => Therapists::query()->where('status', 'available')->orderBy('name')->get(),

            'addOns' => AddOns::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    /**
     * Return available/booked time slots.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],

            'service_id' => ['required', 'integer', 'exists:services,id'],

            'add_on_id' => ['nullable', 'integer', 'exists:add_ons,id'],

            'appointment_date' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], $this->timezone)->startOfDay();

        $service = Services::query()->where('id', $validated['service_id'])->where('status', 'active')->first();

        if (!$service) {
            return response()->json(
                [
                    'message' => 'The selected service is no longer available.',
                    'slots' => [],
                ],
                422,
            );
        }

        $therapist = Therapists::query()->where('id', $validated['therapist_id'])->where('status', 'available')->first();

        if (!$therapist) {
            return response()->json(
                [
                    'message' => 'The selected therapist is no longer available.',
                    'slots' => [],
                ],
                422,
            );
        }

        $addOn = null;

        if (!empty($validated['add_on_id'])) {
            $addOn = AddOns::query()->where('id', $validated['add_on_id'])->where('status', 'active')->first();

            if (!$addOn) {
                return response()->json(
                    [
                        'message' => 'The selected add-on is no longer available.',
                        'slots' => [],
                    ],
                    422,
                );
            }
        }

        $serviceDuration = (int) ($service->duration_minutes ?? 0);
        $addOnDuration = (int) ($addOn?->duration_minutes ?? 0);

        $requiredMinutes = $serviceDuration + $addOnDuration;

        if ($serviceDuration <= 0) {
            return response()->json(
                [
                    'message' => 'The selected service has an invalid duration.',
                    'slots' => [],
                ],
                422,
            );
        }

        $windows = $this->getBookingWindows($date);

        $appointments = $this->getTherapistAppointments((int) $validated['therapist_id'], $date);

        $slots = [];

        $now = Carbon::now($this->timezone);

        foreach ($windows as $window) {
            $cursor = $window['start']->copy();

            while ($cursor->copy()->addMinutes($requiredMinutes)->lte($window['end'])) {
                $slotStart = $cursor->copy();

                $slotEnd = $cursor->copy()->addMinutes($requiredMinutes);

                if ($date->isSameDay($now) && $slotStart->lte($now)) {
                    $cursor->addMinutes($this->slotInterval);

                    continue;
                }

                $isBooked = false;

                foreach ($appointments as $appointment) {
                    [$existingStart, $existingEnd] = $this->getAppointmentRange($appointment);

                    if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                        $isBooked = true;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $slotStart->format('H:i'),

                    'start' => $slotStart->format('Y-m-d H:i:s'),

                    'end' => $slotEnd->format('Y-m-d H:i:s'),

                    'label' => $this->formatTimeRange($slotStart, $slotEnd),

                    'status' => $isBooked ? 'booked' : 'available',
                ];

                $cursor->addMinutes($this->slotInterval);
            }
        }

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'service_duration' => $serviceDuration,
            'addon_duration' => $addOnDuration,
            'required_minutes' => $requiredMinutes,
            'slots' => $slots,
        ]);
    }

    /**
     * Store Quick Book walk-in appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],

            'phone' => ['required', 'string', 'max:20'],

            'email' => ['nullable', 'email', 'max:255'],

            'service_id' => ['required', 'integer', 'exists:services,id'],

            'therapist_id' => ['required', 'integer', 'exists:therapists,id'],

            'level' => ['required', 'string', 'in:gentle,mild,hard'],

            'add_on_id' => ['nullable', 'integer', 'exists:add_ons,id'],

            'has_previous_operations' => ['required', 'string', 'in:yes,no'],

            'body_problem' => ['nullable', 'string', 'max:2000'],

            'appointment_date' => ['required', 'date', 'date_format:Y-m-d'],

            'appointment_time' => ['required', 'date_format:H:i'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        $service = Services::query()->where('id', $validated['service_id'])->where('status', 'active')->first();

        if (!$service) {
            throw ValidationException::withMessages([
                'service_id' => 'The selected service is no longer available.',
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
                'therapist_id' => 'The selected therapist is no longer available.',
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
                    'add_on_id' => 'The selected add-on is no longer available.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Price
        |--------------------------------------------------------------------------
        */

        $servicePrice = (float) $service->price;

        $addOnPrice = (float) ($addOn?->price ?? 0);

        $totalAmount = $servicePrice + $addOnPrice;

        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */

        $serviceDuration = (int) ($service->duration_minutes ?? 0);

        $addOnDuration = (int) ($addOn?->duration_minutes ?? 0);

        $totalDuration = $serviceDuration + $addOnDuration;

        if ($serviceDuration <= 0) {
            throw ValidationException::withMessages([
                'service_id' => 'The selected service has an invalid duration.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Appointment date
        |--------------------------------------------------------------------------
        */

        $bookingDate = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], $this->timezone)->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | Appointment start
        |--------------------------------------------------------------------------
        */

        $appointmentStart = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d') . ' ' . $validated['appointment_time'], $this->timezone);

        /*
        |--------------------------------------------------------------------------
        | Appointment end
        |--------------------------------------------------------------------------
        */

        $appointmentEnd = $appointmentStart->copy()->addMinutes($totalDuration);

        /*
        |--------------------------------------------------------------------------
        | Must fit inside one business window
        |--------------------------------------------------------------------------
        */

        $fitsWindow = false;

        foreach ($this->getBookingWindows($bookingDate) as $window) {
            if ($appointmentStart->gte($window['start']) && $appointmentEnd->lte($window['end'])) {
                $fitsWindow = true;
                break;
            }
        }

        if (!$fitsWindow) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment does not fit within the operating schedule.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Do not allow past appointments
        |--------------------------------------------------------------------------
        */

        if ($appointmentStart->lte(Carbon::now($this->timezone))) {
            throw ValidationException::withMessages([
                'appointment_time' => 'The selected appointment time has already passed.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use ($validated, $service, $therapist, $addOn, $servicePrice, $addOnPrice, $serviceDuration, $addOnDuration, $totalDuration, $totalAmount, $appointmentStart, $appointmentEnd) {
            /*
                |--------------------------------------------------------------------------
                | Lock therapist
                |--------------------------------------------------------------------------
                */

            $lockedTherapist = Therapists::query()->where('id', $therapist->id)->where('status', 'available')->lockForUpdate()->first();

            if (!$lockedTherapist) {
                throw ValidationException::withMessages([
                    'therapist_id' => 'The selected therapist is no longer available.',
                ]);
            }

            /*
                |--------------------------------------------------------------------------
                | Re-check appointments
                |--------------------------------------------------------------------------
                */

            $appointmentDate = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], $this->timezone)->startOfDay();

            $existingAppointments = $this->getTherapistAppointments((int) $lockedTherapist->id, $appointmentDate);

            foreach ($existingAppointments as $existingAppointment) {
                [$existingStart, $existingEnd] = $this->getAppointmentRange($existingAppointment);

                if ($appointmentStart->lt($existingEnd) && $appointmentEnd->gt($existingStart)) {
                    throw ValidationException::withMessages([
                        'appointment_time' => 'The selected time has just been booked. Please choose another time.',
                    ]);
                }
            }

            /*
                |--------------------------------------------------------------------------
                | Walk-in customer
                |--------------------------------------------------------------------------
                */

            $walkInCustomer = WalkInCustomer::query()->where('phone', $validated['phone'])->first();

            if ($walkInCustomer) {
                $walkInCustomer->update([
                    'name' => $validated['customer_name'],

                    'email' => $validated['email'] ?? $walkInCustomer->email,
                ]);
            } else {
                $walkInCustomer = WalkInCustomer::create([
                    'name' => $validated['customer_name'],

                    'phone' => $validated['phone'],

                    'email' => $validated['email'] ?? null,
                ]);
            }

            /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

            $paymentStatus = 'paid';
            $amountPaid = $totalAmount;
            $paidAt = Carbon::now($this->timezone);

            /*
                |--------------------------------------------------------------------------
                | Create appointment
                |--------------------------------------------------------------------------
                */

            $appointment = UsersAppointments::create([
                'user_id' => null,

                'walk_in_customer_id' => $walkInCustomer->id,

                'booking_source' => 'walk_in',

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

                'appointment_date' => $appointmentStart->format('Y-m-d'),

                'appointment_time' => $appointmentStart->format('H:i:s'),

                'appointment_end_time' => $appointmentEnd->format('H:i:s'),

                'payment_method' => 'branch',

                'payment_type' => 'full',

                'payment_amount' => $totalAmount,

                'amount_paid' => $amountPaid,

                'payment_status' => 'paid',

                'paid_at' => $paidAt,

                'paymongo_checkout_session_id' => null,

                'paymongo_payment_id' => null,

                'paymongo_reference_number' => null,

                'paymongo_checkout_expires_at' => null,

                'status' => AppointmentStatus::CONFIRMED,
            ]);

            return [
                'appointment' => $appointment,

                'walkInCustomer' => $walkInCustomer,
            ];
        });

        $appointment = $result['appointment'];

        $walkInCustomer = $result['walkInCustomer'];

        /*
        |--------------------------------------------------------------------------
        | Send registration invitation
        |--------------------------------------------------------------------------
        |
        | Only send an invitation if:
        |
        | 1. Customer supplied an email
        | 2. Customer does not already have an account
        |
        */

        $invitationSent = false;

        if (!empty($walkInCustomer->email) && !$walkInCustomer->registered_user_id) {
            /*
            |--------------------------------------------------------------------------
            | Generate RAW token
            |--------------------------------------------------------------------------
            */

            $rawToken = Str::random(64);

            /*
            |--------------------------------------------------------------------------
            | Store HASHED token
            |--------------------------------------------------------------------------
            */

            $walkInCustomer->update([
                'registration_token' => hash('sha256', $rawToken),

                'registration_token_expires_at' => Carbon::now($this->timezone)->addHours(24),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Generate registration URL using RAW token
            |--------------------------------------------------------------------------
            */

            $registrationUrl = route('walk-in.register', [
                'token' => $rawToken,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Send email
            |--------------------------------------------------------------------------
            */

            Mail::to($walkInCustomer->email)->send(new WalkInRegistrationInvitation($walkInCustomer->fresh(), $registrationUrl));

            $invitationSent = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $message = 'Walk-in appointment created successfully.';

        if ($invitationSent) {
            $message .= ' A registration invitation has been sent to the customer.';
        } elseif (empty($walkInCustomer->email)) {
            $message .= ' No registration invitation was sent because the customer has no email address.';
        } elseif ($walkInCustomer->registered_user_id) {
            $message .= ' The customer already has a Padayon account, so no registration invitation was sent.';
        }

        return redirect()->route('staff.quick-book')->with('success', $message);
    }

    /**
     * Get business windows.
     *
     * 12:00 AM - 1:00 AM
     * 1:00 PM - 12:00 AM
     */
    private function getBookingWindows(Carbon $date): array
    {
        $date = $date->copy()->setTimezone($this->timezone)->startOfDay();

        return [
            /*
            |--------------------------------------------------------------------------
            | 12:00 AM - 1:00 AM
            |--------------------------------------------------------------------------
            */

            [
                'start' => $date->copy()->setTime(0, 0, 0),

                'end' => $date->copy()->setTime(1, 0, 0),
            ],

            /*
            |--------------------------------------------------------------------------
            | 1:00 PM - 12:00 AM
            |--------------------------------------------------------------------------
            */

            [
                'start' => $date->copy()->setTime(13, 0, 0),

                'end' => $date->copy()->addDay()->startOfDay(),
            ],
        ];
    }

    /**
     * Get appointments that block the therapist.
     */
    private function getTherapistAppointments(int $therapistId, Carbon $date)
    {
        $nonBlockingStatuses = [AppointmentStatus::CANCELLED, AppointmentStatus::REJECTED, AppointmentStatus::FAILED, AppointmentStatus::NO_SHOW];

        $nonBlockingValues = collect($nonBlockingStatuses)->map(fn($status) => $status instanceof \BackedEnum ? $status->value : $status)->all();

        $previousDate = $date->copy()->subDay()->startOfDay();

        $nextDate = $date->copy()->addDay()->startOfDay();

        return UsersAppointments::query()
            ->where('therapist_id', $therapistId)
            ->whereBetween('appointment_date', [$previousDate->format('Y-m-d'), $nextDate->format('Y-m-d')])
            ->whereNotIn('status', $nonBlockingValues)
            ->get();
    }

    /**
     * Get exact appointment range.
     *
     * Handles appointments crossing midnight.
     */
    private function getAppointmentRange(UsersAppointments $appointment): array
    {
        if ($appointment->appointment_date instanceof Carbon) {
            $date = $appointment->appointment_date->format('Y-m-d');
        } else {
            $date = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');
        }

        $startTime = $this->normalizeTime($appointment->appointment_time);

        $endTime = $this->normalizeTime($appointment->appointment_end_time);

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $startTime, $this->timezone);

        $end = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $endTime, $this->timezone);

        /*
        |--------------------------------------------------------------------------
        | Cross-midnight
        |--------------------------------------------------------------------------
        */

        if ($end->lte($start)) {
            $end->addDay();
        }

        return [$start, $end];
    }

    /**
     * Normalize database time to H:i:s.
     */
    private function normalizeTime($time): string
    {
        if ($time instanceof Carbon) {
            return $time->format('H:i:s');
        }

        $time = trim((string) $time);

        if (str_contains($time, ' ')) {
            $time = substr($time, strrpos($time, ' ') + 1);
        }

        if (strlen($time) === 5) {
            $time .= ':00';
        }

        return $time;
    }

    /**
     * Format slot label.
     */
    private function formatTimeRange(Carbon $start, Carbon $end): string
    {
        return $start->format('g:i A') . ' – ' . $end->format('g:i A');
    }
}
