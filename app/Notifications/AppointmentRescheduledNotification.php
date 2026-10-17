<?php 
namespace App\Notifications;

use App\Models\UsersAppointments;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
{
    $this->appointment->loadMissing([
        'service',
        'therapist',
        'addOn',
    ]);

    return (new MailMessage())
        ->subject('Your Appointment Has Been Rescheduled')
        ->view('emails.appointment.rescheduled', [
            'appointment' => $this->appointment,
            'notifiable' => $notifiable,
            'websiteSettings' => WebsiteSetting::first(),
        ]);
}

    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => 'Your appointment has been rescheduled successfully.',
            'status' => $this->appointment->status->value ?? $this->appointment->status,
        ];
    }
}
