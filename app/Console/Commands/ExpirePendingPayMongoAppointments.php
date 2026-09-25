<?php

namespace App\Console\Commands;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Models\UsersAppointments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePendingPayMongoAppointments extends Command
{
    protected $signature = 'appointments:expire-pending-payments';

    protected $description = 'Expire abandoned pending GCash payments';

    public function handle(): int
    {
        $appointments = UsersAppointments::query()
            ->where('payment_method', 'gcash')
            ->where('payment_status', 'pending')
            ->where('status', AppointmentStatus::PENDING)
            ->whereNotNull('paymongo_checkout_expires_at')
            ->where(
                'paymongo_checkout_expires_at',
                '<=',
                now('Asia/Manila')
            )
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update([
                'payment_status' => 'failed',
                'status' => AppointmentStatus::FAILED,
            ]);

            Log::info('Expired abandoned PayMongo payment.', [
                'appointment_id' => $appointment->id,
                'reference_number' => $appointment->paymongo_reference_number,
                'checkout_session_id' => $appointment->paymongo_checkout_session_id,
            ]);
        }

        $this->info(
            "Expired {$appointments->count()} pending PayMongo appointment(s)."
        );

        return self::SUCCESS;
    }
}