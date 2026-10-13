<?php

namespace App\Actions\Admin\Transaction;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\UsersAppointments;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarkTransactionAsPaid
{
    public function execute(UsersAppointments $appointment): UsersAppointments
    {
        return DB::transaction(function () use ($appointment) {
            $appointment = UsersAppointments::query()->lockForUpdate()->findOrFail($appointment->id);

            // Only confirmed appointments can be paid through this action.
            if ($appointment->status !== AppointmentStatus::CONFIRMED) {
                throw ValidationException::withMessages([
                    'payment' => 'Only confirmed appointments can be marked as paid.',
                ]);
            }

            $paymentMethod = strtolower((string) $appointment->payment_method);

            $paymentType = strtolower((string) $appointment->payment_type);

            $isCounterPayment = $paymentMethod === 'branch';
            $isDownpayment = $paymentType === 'downpayment';

            if (!$isCounterPayment && !$isDownpayment) {
                throw ValidationException::withMessages([
                    'payment' => 'This appointment is not eligible for this payment action.',
                ]);
            }

            $servicePrice = (float) ($appointment->service_price ?? 0);
            $addonPrice = (float) ($appointment->addons_price ?? 0);

            $totalAppointmentAmount = $servicePrice + $addonPrice;

            $previousAmountPaid = (float) ($appointment->amount_paid ?? 0);

            $remainingBalance = max(0, $totalAppointmentAmount - $previousAmountPaid);

            if ($remainingBalance <= 0) {
                return $appointment->fresh();
            }

            $appointment->update([
                'amount_paid' => $totalAppointmentAmount,
                'payment_status' => 'paid',
                'payment_type' => 'full',
                'paid_at' => now('Asia/Manila'),
            ]);

            $appointment->load(['user', 'service', 'therapist', 'addOn']);

            if ($appointment->user) {
                $appointment->user->notify(new \App\Notifications\AppointmentBalancePaidNotification($appointment, $previousAmountPaid, $remainingBalance));
            }

            return $appointment->fresh();
        });
    }
}
