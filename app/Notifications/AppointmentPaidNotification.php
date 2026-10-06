<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment) {}

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
    
        return (new MailMessage)
            ->subject('Payment Received - E-Receipt #' . $reference)
            ->view('emails.appointment.paid', [
                'appointment' => $this->appointment,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Received',
            'message' => 'Your payment has been successfully recorded.',

            'appointment_id' => $this->appointment->id,

            'reference' => str_pad((string) $this->appointment->id, 6, '0', STR_PAD_LEFT),

            'service' => $this->appointment->service?->name ?? 'N/A',

            'therapist' => $this->appointment->therapist?->name ?? 'N/A',

            'payment_method' => $this->appointment->payment_method,

            'payment_type' => $this->appointment->payment_type,

            'amount_paid' => (float) ($this->appointment->amount_paid ?? 0),

            'payment_status' => $this->appointment->payment_status,

            'paid_at' => $this->appointment->paid_at ? $this->appointment->paid_at->format('F d, Y h:i A') : null,

            'paymongo_reference_number' => $this->appointment->paymongo_reference_number,
        ];
    }
}
