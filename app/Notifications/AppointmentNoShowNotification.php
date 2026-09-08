<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentNoShowNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Appointment Was No Show')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your appointment has been no show.')
            ->line('Service: ' . ($this->appointment->service->name ?? 'N/A'))
            ->line('Therapist: ' . ($this->appointment->therapist->name ?? 'N/A'))
            ->line('Date: ' . optional($this->appointment->appointment_date)->format('F d, Y'))
            ->line('Time: ' . Carbon::parse($this->appointment->appointment_time)->format('h:i A'))
            ->line('Please contact us if you need assistance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Your Appointment Was No Show',
            'message' => 'Your appointment has been no show',
            'service' => $this->appointment->service->name ?? 'N/A',
            'therapist' => $this->appointment->therapist->name ?? 'N/A',
            'date' => optional($this->appointment->appointment_date)->format('F d, Y'),
            'time' => Carbon::parse($this->appointment->appointment_time)->format('h:i A'),
            'addon' => $this->appointment->addOn->name ?? 'None',
        ];
    }
}
