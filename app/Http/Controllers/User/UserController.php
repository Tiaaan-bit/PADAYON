<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\ImagePost;

class UserController extends Controller
{
    private string $timezone = 'Asia/Manila';

    public function dashboard()
    {
        $user = Auth::user();

        $now = Carbon::now($this->timezone);
        $today = $now->toDateString();

        $appointments = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->where('user_id', $user->id)
            ->get();

        $appointments = $appointments->map(function ($appointment) {
            $date = Carbon::parse($appointment->appointment_date, $this->timezone)->format('Y-m-d');

            $start = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . Carbon::parse($appointment->appointment_time, $this->timezone)->format('H:i:s'), $this->timezone);

            $appointment->start_datetime = $start->copy();

            if ($appointment->appointment_end_time) {
                $end = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . Carbon::parse($appointment->appointment_end_time, $this->timezone)->format('H:i:s'), $this->timezone);

                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }

                $appointment->end_datetime = $end;
            } else {
                $duration = (int) ($appointment->service_duration_minutes ?? 0) + (int) ($appointment->addons_duration_minutes ?? 0);

                $appointment->end_datetime = $start->copy()->addMinutes($duration);
            }

            return $appointment;
        });

        $imagePosts = ImagePost::query()->whereNotNull('published_at')->where('published_at', '<=', now('Asia/Manila'))->latest('published_at')->get();

        $activeStatuses = [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED];

        $todayAppointments = $appointments
            ->filter(function ($appointment) use ($today, $activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true) && $appointment->start_datetime->toDateString() === $today;
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();

        $upcomingAppointments = $appointments
            ->filter(function ($appointment) use ($today, $now, $activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true) && $appointment->start_datetime->greaterThan($now) && $appointment->start_datetime->toDateString() !== $today;
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->take(5)
            ->values();

        $calendarAppointments = $appointments
            ->filter(function ($appointment) use ($activeStatuses) {
                return in_array($appointment->status, $activeStatuses, true);
            })
            ->sortBy(function ($appointment) {
                return $appointment->start_datetime->timestamp;
            })
            ->values();

        $calendarEvents = $calendarAppointments
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,

                    'date' => $appointment->start_datetime->format('Y-m-d'),

                    'title' => $appointment->service->name ?? 'Appointment',

                    'description' => $appointment->service->description ?? 'Appointment',

                    'time' => $appointment->start_datetime->format('h:i A'),

                    'end_time' => $appointment->end_datetime->format('h:i A'),

                    'status' => $appointment->status?->value ?? 'pending',

                    'user' => $appointment->user->name ?? 'User',

                    'therapist' => $appointment->therapist->name ?? 'Not assigned',
                ];
            })
            ->values();

        $posts = Post::query()->whereNotNull('published_at')->where('published_at', '<=', $now)->latest('published_at')->take(5)->get();

        $therapists = Therapists::where('status', 'available')->orderBy('name')->take(6)->get();

        return view('user.dashboard', compact('todayAppointments', 'upcomingAppointments', 'calendarEvents', 'posts', 'therapists', 'imagePosts'));
    }
}
