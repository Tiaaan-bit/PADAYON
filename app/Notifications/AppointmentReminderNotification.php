<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use App\Models\WebsiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public UsersAppointments $appointment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Appointment Reminder',
            'message' => 'Your massage appointment is coming up within 24 hours.',
            'appointment_id' => $this->appointment->id,
            'appointment_date' => Carbon::parse(
                $this->appointment->appointment_date
            )->format('Y-m-d'),
            'appointment_time' => $this->appointment->appointment_time,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $websiteSettings = WebsiteSetting::first();

        return (new MailMessage)
            ->subject('Reminder: Your Upcoming Padayon Massage Appointment')
            ->view('emails.appointment.reminder', [
                'notifiable' => $notifiable,
                'appointment' => $this->appointment,
                'websiteSettings' => $websiteSettings,
            ]);
    }
}