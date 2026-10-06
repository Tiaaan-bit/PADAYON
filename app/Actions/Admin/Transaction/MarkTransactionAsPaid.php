<?php

namespace App\Actions\Admin\Transaction;

use App\Models\UsersAppointments;
use Illuminate\Support\Facades\DB;

class MarkTransactionAsPaid
{
    public function execute(UsersAppointments $appointment): UsersAppointments
    {
        return DB::transaction(function () use ($appointment) {
            /*
            |--------------------------------------------------------------------------
            | Calculate appointment total
            |--------------------------------------------------------------------------
            */

            $servicePrice = (float) ($appointment->service_price ?? 0);
            $addonPrice = (float) ($appointment->addons_price ?? 0);

            $totalAppointmentAmount = $servicePrice + $addonPrice;

            /*
            |--------------------------------------------------------------------------
            | Get amount already paid
            |--------------------------------------------------------------------------
            |
            | Example:
            | Total appointment = ₱600
            | Previous downpayment = ₱300
            | Remaining balance = ₱300
            |
            */

            $previousAmountPaid = (float) ($appointment->amount_paid ?? 0);

            $remainingBalance = max(0, $totalAppointmentAmount - $previousAmountPaid);

            /*
            |--------------------------------------------------------------------------
            | Safety check
            |--------------------------------------------------------------------------
            */

            if ($remainingBalance <= 0) {
                return $appointment->fresh();
            }

            /*
            |--------------------------------------------------------------------------
            | Mark appointment as fully paid
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'amount_paid' => $totalAppointmentAmount,
                'payment_status' => 'paid',
                'payment_type' => 'full',
                'paid_at' => now('Asia/Manila'),
            ]);

            $appointment->refresh();

            /*
            |--------------------------------------------------------------------------
            | Load relationships needed by the notification
            |--------------------------------------------------------------------------
            */

            $appointment->load(['user', 'service', 'therapist', 'addOn']);

            /*
            |--------------------------------------------------------------------------
            | Send final balance payment email
            |--------------------------------------------------------------------------
            */

            if ($appointment->user) {
                $appointment->user->notify(new \App\Notifications\AppointmentBalancePaidNotification($appointment, $previousAmountPaid, $remainingBalance));
            }

            return $appointment;
        });
    }
}
