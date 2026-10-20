<?php

namespace App\Services;

use App\Models\Therapists;
use Carbon\Carbon;
use BackedEnum;
class TherapistAppointmentService
{
    /**
     * Retrieve today's appointments for the authenticated therapist.
     */
    public function getTodaysAppointments(Therapists $therapist): array
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        return $therapist
            ->appointments()
            ->with(['user', 'walkInCustomer', 'service', 'addOn'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($appointment) {
                $status = $appointment->status;

                if ($status instanceof BackedEnum) {
                    $status = $status->value;
                }

                $customerName = $appointment->user?->name ?? ($appointment->walkInCustomer?->name ?? 'Guest customer');

                return [
                    'customer' => $customerName,
                    'service' => $appointment->service?->name ?? 'Service not specified',
                    'add_on' => $appointment->addOn?->name,
                    'date' => $appointment->appointment_date ? $appointment->appointment_date->format('F j, Y') : null,
                    'start_time' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('g:i A') : 'Time not specified',
                    'end_time' => $appointment->appointment_end_time ? Carbon::parse($appointment->appointment_end_time)->format('g:i A') : null,
                    'status' => (string) ($status ?? 'unknown'),
                ];
            })
            ->all();
    }
}
