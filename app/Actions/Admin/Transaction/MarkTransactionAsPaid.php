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
            | Calculate the actual appointment total
            |--------------------------------------------------------------------------
            */

            $servicePrice = (float) ($appointment->service_price ?? 0);

            $addonPrice = (float) ($appointment->addons_price ?? 0);

            $totalAppointmentAmount = $servicePrice + $addonPrice;

            /*
            |--------------------------------------------------------------------------
            | Mark the entire appointment as fully paid
            |--------------------------------------------------------------------------
            |
            | If the appointment was originally a downpayment, change the
            | payment type to full once the remaining balance is paid.
            |
            */

            $appointment->update([
                'amount_paid' => $totalAppointmentAmount,
                'payment_status' => 'paid',
                'payment_type' => 'full',
                'paid_at' => now(),
            ]);

            return $appointment->fresh();
        });
    }
}
