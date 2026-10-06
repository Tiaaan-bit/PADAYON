<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBalancePaidNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment, public float $previousAmountPaid, public float $additionalPayment) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
{
    $reference = str_pad(
        (string) $this->appointment->id,
        6,
        '0',
        STR_PAD_LEFT
    );

    $servicePrice = (float) ($this->appointment->service_price ?? 0);
    $addonPrice = (float) ($this->appointment->addons_price ?? 0);

    $totalAmount = $servicePrice + $addonPrice;
    $totalPaid = (float) ($this->appointment->amount_paid ?? 0);

    $remainingBalance = max(
        0,
        $totalAmount - $totalPaid
    );

    return (new MailMessage)
        ->subject(
            'Remaining Balance Paid - Final E-Receipt #' . $reference
        )
        ->view('emails.appointment.balance-paid', [
            'appointment' => $this->appointment,
            'notifiable' => $notifiable,
            'previousAmountPaid' => $this->previousAmountPaid,
            'additionalPayment' => $this->additionalPayment,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
        ]);
}

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Remaining Balance Paid',
            'message' => 'Your remaining appointment balance has been fully paid.',
            'appointment_id' => $this->appointment->id,
            'reference' => str_pad((string) $this->appointment->id, 6, '0', STR_PAD_LEFT),
            'service' => $this->appointment->service?->name ?? 'N/A',
            'therapist' => $this->appointment->therapist?->name ?? 'N/A',
            'previous_amount_paid' => $this->previousAmountPaid,
            'additional_payment' => $this->additionalPayment,
            'total_paid' => (float) ($this->appointment->amount_paid ?? 0),
            'payment_status' => $this->appointment->payment_status,
            'paid_at' => $this->appointment->paid_at ? $this->appointment->paid_at->format('F d, Y h:i A') : null,
        ];
    }
}
