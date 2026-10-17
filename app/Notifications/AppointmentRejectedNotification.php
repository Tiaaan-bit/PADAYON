<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use App\Models\WebsiteSetting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $websiteSettings = WebsiteSetting::first();

        return (new MailMessage)
            ->subject('Your Appointment Was Rejected')
            ->view('emails.appointment.rejected', [
                'appointment' => $this->appointment,
                'notifiable' => $notifiable,
                'websiteSettings' => $websiteSettings,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Your Appointment Was Rejected',
            'message' => 'Unfortunately, your appointment has been rejected.',
            'appointment_id' => $this->appointment->id,
            'service' => $this->appointment->service?->name ?? 'N/A',
            'therapist' => $this->appointment->therapist?->name ?? 'N/A',
            'date' => $this->appointment->appointment_date
                ? Carbon::parse($this->appointment->appointment_date)->format('F d, Y')
                : 'N/A',
            'time' => $this->appointment->appointment_time
                ? Carbon::parse($this->appointment->appointment_time)->format('h:i A')
                : 'N/A',
            'addon' => $this->appointment->addOn?->name ?? 'None',
        ];
    }
}
