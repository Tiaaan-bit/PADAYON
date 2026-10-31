<?php

namespace App\Notifications;

use App\Models\UsersAppointments;
use App\Models\WebsiteSetting;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\Channels\SmsChannel;

class AppointmentNoShowNotification extends Notification
{
    use Queueable;

    public function __construct(public UsersAppointments $appointment) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', SmsChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $websiteSettings = WebsiteSetting::first();

        return new MailMessage()->subject('Your Appointment Was Marked as No-Show')->view('emails.appointment.no-show', [
            'appointment' => $this->appointment,
            'notifiable' => $notifiable,
            'websiteSettings' => $websiteSettings,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Your Appointment Was Marked as No-Show',
            'message' => 'Your appointment has been marked as a no-show.',
            'appointment_id' => $this->appointment->id,
            'service' => $this->appointment->service?->name ?? 'N/A',
            'description' => $this->appointment->service?->description ?? 'N/A',
            'therapist' => $this->appointment->therapist?->name ?? 'N/A',
            'date' => $this->appointment->appointment_date ? Carbon::parse($this->appointment->appointment_date)->format('F d, Y') : 'N/A',
            'time' => $this->appointment->appointment_time ? Carbon::parse($this->appointment->appointment_time)->format('h:i A') : 'N/A',
            'end_time' => $this->appointment->appointment_end_time ? Carbon::parse($this->appointment->appointment_end_time)->format('h:i A') : 'N/A',
            'addon' => $this->appointment->addOn?->name ?? 'None',
        ];
    }

    public function sendConfirmationSms(object $notifiable): void
    {
        if (!config('services.sms.enabled')) {
            return;
        }

        $phone = $notifiable->phone ?? null;

        if (blank($phone)) {
            Log::warning('Appointment confirmation SMS skipped: customer phone is missing.', ['appointment_id' => $this->appointment->id]);

            return;
        }

        $appointment = $this->appointment->loadMissing(['service', 'therapist']);

        $appointment_id = $appointment->id ?? 'No ID';

        $date = $appointment->appointment_date ? Carbon::parse($appointment->appointment_date)->format('M d, Y') : 'N/A';

        $time = $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A';

        $endtime = $appointment->appointment_end_time ? Carbon::parse($appointment->appointment_end_time)->format('h:i A') : 'N/A';

        $service = $appointment->service?->name ?? 'Massage service';

        $description = $appointment->service?->description ?? '';

        $therapist = $appointment->therapist?->name ?? 'To be assigned';

        $message = 'Padayon Massage Center: Your appointment has been marked as a no-show. ' . "#: {$appointment_id} " . "Service: {$service}-{$description} " . "Therapist: {$therapist}. " . "Date: {$date}. Time: {$time}-{$endtime} " . 'Thank you for choosing Padayon!';

        $sent = app(SmsService::class)->send($phone, $message);

        if (!$sent) {
            Log::warning('Appointment confirmation SMS was not accepted by UniSMS.', ['appointment_id' => $appointment->id]);
        }
    }
}
