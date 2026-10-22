<?php

namespace App\Services\Therapist;

use App\Models\Therapists;
use App\Models\UsersAppointments;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Throwable;

class TherapistAppointmentService
{
    private const TIMEZONE = 'Asia/Manila';

    private const EXCLUDED_STATUSES = ['cancelled', 'rejected', 'failed', 'no_show'];

    /**
     * Understand therapist questions and return the relevant database context.
     */

    public function contextForQuestion(Therapists $therapist, string $question): ?array
    {
        $question = mb_strtolower(trim($question));

        $question = preg_replace('/[^\p{L}\p{N}\s:\/-]/u', ' ', $question) ?? $question;

        $question = preg_replace('/\s+/u', ' ', $question) ?? $question;

        if ($question === '') {
            return null;
        }

        $today = Carbon::now(self::TIMEZONE)->startOfDay();

        // Appointment status questions.
        $status = $this->detectStatus($question);

        if ($status !== null) {
            $date = $this->extractSpecificDate($question);

            if (preg_match('/\b(today|todays|tonight)\b/u', $question)) {
                $date = $today->copy();
            } elseif (preg_match('/\b(tomorrow|tomorrows)\b/u', $question)) {
                $date = $today->copy()->addDay();
            }

            if ($date !== null) {
                return $this->getAppointmentsByStatus($therapist, $status, $date->toDateString(), $date->toDateString());
            }

            return $this->getAppointmentsByStatus($therapist, $status);
        }

        // Free-time questions.
        if (preg_match('/\b(free time|free slot|free slots|gaps between|' . 'available time|when am i free|when am i available|' . 'am i free|do i have free time|any free time|' . 'any openings|open slot|open slots)\b/u', $question)) {
            $date = $this->extractSpecificDate($question);

            if (preg_match('/\b(tomorrow|tomorrows)\b/u', $question)) {
                $date = $today->copy()->addDay();
            } elseif (preg_match('/\b(today|todays|tonight)\b/u', $question)) {
                $date = $today->copy();
            }

            return $this->getFreeTimeContext($therapist, ($date ?? $today)->toDateString());
        }

        // Workload questions.
        if (preg_match('/\b(workload|busiest day|busiest|how busy|' . 'appointments per day|sessions per day|' . 'most appointments|least appointments)\b/u', $question)) {
            if (preg_match('/\bnext week\b/u', $question)) {
                return $this->getNextWeekContext($therapist);
            }

            return $this->getWorkloadContext($therapist);
        }

        // Next appointment or client.
        if (preg_match('/\b(next appointment|next client|next customer|' . 'next session|next booking|nearest appointment|' . 'upcoming appointment|upcoming client|upcoming session|' . 'when is my next|who is my next|what is my next|' . 'what service is next|who am i seeing next)\b/u', $question)) {
            return $this->getNextAppointmentContext($therapist);
        }

        // Specific dates and weekdays.
        $specificDate = $this->extractSpecificDate($question);

        if ($specificDate !== null) {
            return $this->getAppointmentsForDate($therapist, $specificDate->toDateString());
        }

        // Weekly schedules.
        if (preg_match('/\b(this week|weekly|week schedule|schedule this week|' . 'rest of the week|remaining days this week|next week|' . 'how many.*this week|how many.*next week|' . 'appointments.*this week|appointments.*next week)\b/u', $question)) {
            if (preg_match('/\bnext week\b/u', $question)) {
                return $this->getNextWeekContext($therapist);
            }

            return $this->getWeekContext($therapist);
        }

        // Tomorrow's schedule.
        if (preg_match('/\b(tomorrow|tomorrows|next day)\b/u', $question)) {
            return $this->getTomorrowContext($therapist);
        }

        // Today's schedule and natural count questions.
        if (preg_match('/\b(today|todays|tonight|this day)\b/u', $question) || preg_match('/\b(who am i seeing|what do i have|' . 'am i working|am i busy|how many do i have|' . 'do i have any|how many appointments|' . 'how many clients|how many bookings|' . 'what is my schedule|whats my schedule)\b/u', $question) || preg_match('/^(my )?(schedule|appointments|bookings|' . 'clients|customers|sessions)( today)?$/u', $question)) {
            return $this->getTodayContext($therapist);
        }

        // Unrecognized questions should not trigger a guessed database query.
        return null;
    }

    /**
     * Today's appointments.
     */
    public function getTodayContext(Therapists $therapist): array
    {
        $today = Carbon::now(self::TIMEZONE)->startOfDay();

        $records = $this->appointmentsBetween($therapist, $today->toDateString(), $today->toDateString());

        return array_merge(
            [
                'query' => 'today',
                'date' => $today->format('F j, Y'),
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * Tomorrow's appointments.
     */
    public function getTomorrowContext(Therapists $therapist): array
    {
        $tomorrow = Carbon::now(self::TIMEZONE)->addDay()->startOfDay();

        $records = $this->appointmentsBetween($therapist, $tomorrow->toDateString(), $tomorrow->toDateString());

        return array_merge(
            [
                'query' => 'tomorrow',
                'date' => $tomorrow->format('F j, Y'),
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * This week's appointments, Monday through Sunday.
     */
    public function getWeekContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);

        $records = $this->appointmentsBetween($therapist, $start->toDateString(), $end->toDateString());

        return array_merge(
            [
                'query' => 'this_week',
                'date_range' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * Next week's appointments.
     */
    public function getNextWeekContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $start = $now->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        $records = $this->appointmentsBetween($therapist, $start->toDateString(), $end->toDateString());

        return array_merge(
            [
                'query' => 'next_week',
                'date_range' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * The next appointment that has not been cancelled or invalidated.
     */
    public function getNextAppointmentContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);
        $today = $now->toDateString();

        $appointment = $this->baseQuery($therapist)
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->where(function (Builder $query) use ($today, $now) {
                $query->whereDate('appointment_date', '>', $today)->orWhere(function (Builder $todayQuery) use ($today, $now) {
                    $todayQuery->whereDate('appointment_date', $today)->where('appointment_time', '>=', $now->format('H:i:s'));
                });
            })
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $records = $appointment ? [$this->formatAppointment($appointment)] : [];

        return array_merge(
            [
                'query' => 'next_appointment',
                'date' => $now->format('F j, Y'),
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * Appointments on a specific date.
     */
    public function getAppointmentsForDate(Therapists $therapist, string $date): array
    {
        $day = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        $records = $this->appointmentsBetween($therapist, $day->toDateString(), $day->toDateString());

        return array_merge(
            [
                'query' => 'specific_date',
                'date' => $day->format('l, F j, Y'),
                'timezone' => self::TIMEZONE,
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * Appointments matching a status.
     *
     * Without a date, pending and confirmed results are limited to
     * today and future appointments. Other statuses can include history.
     */
    public function getAppointmentsByStatus(Therapists $therapist, string $status, ?string $startDate = null, ?string $endDate = null): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $query = $this->baseQuery($therapist)->where('status', $status);

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        } elseif (in_array($status, ['pending', 'confirmed'], true)) {
            $query->whereDate('appointment_date', '>=', $now->toDateString());
        }

        $appointments = $query->orderBy('appointment_date')->orderBy('appointment_time')->get();

        $records = $appointments->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))->all();

        return array_merge(
            [
                'query' => 'appointments_by_status',
                'requested_status' => $status,
                'timezone' => self::TIMEZONE,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
            $this->summarizeAppointments($records),
        );
    }

    /**
     * Summarize this week's workload by date.
     */
    public function getWorkloadContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);

        $records = $this->appointmentsBetween($therapist, $start->toDateString(), $end->toDateString());

        $dailyCounts = [];

        $scheduledRecords = array_values(array_filter($records, fn(array $record): bool => !in_array(strtolower((string) ($record['status'] ?? 'unknown')), self::EXCLUDED_STATUSES, true)));

        foreach ($records as $record) {
            if (empty($record['date_iso'])) {
                continue;
            }

            $date = $record['date_iso'];

            $dailyCounts[$date] = ($dailyCounts[$date] ?? 0) + 1;
        }

        $days = [];

        foreach ($dailyCounts as $date => $count) {
            $days[] = [
                'date' => $date,
                'day' => Carbon::parse($date, self::TIMEZONE)->format('l'),
                'appointments' => $count,
            ];
        }

        usort($days, fn(array $a, array $b) => $b['appointments'] <=> $a['appointments']);

        return [
            'query' => 'workload',
            'date_range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'timezone' => self::TIMEZONE,
            'total_appointments' => count($scheduledRecords),
            'days_with_appointments' => count($days),
            'busiest_day' => $days[0] ?? null,
            'daily_counts' => $days,
            'status_counts' => $this->summarizeAppointments($records)['status_counts'],
        ];
    }

    /**
     * Find gaps between appointments on a particular date.
     *
     * This only calculates gaps between scheduled sessions. It does not
     * assume that the therapist is available outside business hours.
     */
    public function getFreeTimeContext(Therapists $therapist, string $date): array
    {
        $day = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        $appointments = $this->baseQuery($therapist)->whereDate('appointment_date', $day->toDateString())->whereNotIn('status', self::EXCLUDED_STATUSES)->orderBy('appointment_time')->get()->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))->filter(fn(array $record) => !empty($record['start_time']) && !empty($record['end_time']) && $record['start_time'] !== 'Time not specified')->values()->all();

        $gaps = [];
        $previousEnd = null;

        foreach ($appointments as $appointment) {
            $start = Carbon::parse($day->toDateString() . ' ' . $appointment['start_time'], self::TIMEZONE);

            $end = Carbon::parse($day->toDateString() . ' ' . $appointment['end_time'], self::TIMEZONE);

            // An appointment may end after midnight.
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            if ($previousEnd !== null && $start->greaterThan($previousEnd)) {
                $minutes = (int) $previousEnd->diffInMinutes($start);

                $gaps[] = [
                    'start_time' => $previousEnd->format('g:i A'),
                    'end_time' => $start->format('g:i A'),
                    'duration_minutes' => $minutes,
                    'duration' => $this->formatDuration($minutes),
                ];
            }

            if ($previousEnd === null || $end->greaterThan($previousEnd)) {
                $previousEnd = $end;
            }
        }

        return [
            'query' => 'free_time_gaps',
            'date' => $day->format('l, F j, Y'),
            'timezone' => self::TIMEZONE,
            'total_appointments' => count($appointments),
            'total_gaps' => count($gaps),
            'free_time_gaps' => $gaps,
            'note' => 'These are gaps between scheduled appointments, not guaranteed availability outside working hours.',
        ];
    }

    /**
     * Summarize appointment counts and statuses for the AI.
     */
    private function summarizeAppointments(array $records): array
    {
        $statuses = [];

        foreach ($records as $record) {
            $status = strtolower((string) ($record['status'] ?? 'unknown'));

            $statuses[$status] = ($statuses[$status] ?? 0) + 1;
        }

        return [
            'total_appointments' => count($records),
            'status_counts' => $statuses,
            'confirmed_appointments' => $statuses['confirmed'] ?? 0,
            'pending_appointments' => $statuses['pending'] ?? 0,
            'appointments' => $records,
        ];
    }

    /**
     * Determine the requested status from the question.
     */
    private function detectStatus(string $question): ?string
    {
        $patterns = [
            'no_show' => '/\b(no show|no shows|no_show|missed appointments)\b/u',
            'cancelled' => '/\b(cancelled|canceled|cancellations)\b/u',
            'rejected' => '/\b(rejected|declined)\b/u',
            'failed' => '/\b(failed|failure)\b/u',
            'pending' => '/\b(pending|awaiting confirmation|waiting for confirmation|unconfirmed)\b/u',
            'confirmed' => '/\b(confirmed|confirmation)\b/u',
            'completed' => '/\b(completed|finished|concluded)\b/u',
        ];

        foreach ($patterns as $status => $pattern) {
            if (preg_match($pattern, $question)) {
                return $status;
            }
        }

        return null;
    }

    /**
     * Extract common explicit dates and relative weekdays from a question.
     */

    private function extractSpecificDate(string $question): ?Carbon
    {
        $timezone = self::TIMEZONE;
        $now = Carbon::now($timezone);
        $weekdays = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        // Explicit weekday phrases.
        if (preg_match('/\b(next|this)\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/u', $question, $matches)) {
            $modifier = $matches[1];
            $weekday = $weekdays[$matches[2]];

            $date = $now->copy()->startOfWeek(Carbon::MONDAY);

            if ($modifier === 'next') {
                $date->addWeek();
            }

            return $date->addDays($weekday - Carbon::MONDAY)->startOfDay();
        }

        $patterns = ['/\b(\d{4}-\d{2}-\d{2})\b/u', '/\b(\d{1,2}\/\d{1,2}\/\d{2,4})\b/u', '/\b((?:january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{1,2}(?:,?\s+\d{4})?)\b/u', '/\b(\d{1,2}\s+(?:january|february|march|april|may|june|july|august|september|october|november|december)(?:\s+\d{4})?)\b/u'];

        foreach ($patterns as $pattern) {
            if (!preg_match($pattern, $question, $matches)) {
                continue;
            }

            try {
                return Carbon::parse($matches[1], $timezone)->startOfDay();
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Convert a duration into a voice-friendly string.
     */
    private function formatDuration(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return "{$remainingMinutes} minutes";
        }

        if ($remainingMinutes === 0) {
            return $hours === 1 ? '1 hour' : "{$hours} hours";
        }

        return "{$hours} hours and {$remainingMinutes} minutes";
    }

    /**
     * Always scope queries to the supplied authenticated therapist.
     */
    private function baseQuery(Therapists $therapist): Builder
    {
        return $therapist
            ->appointments()
            ->with(['user', 'walkInCustomer', 'service', 'addOn'])
            ->getQuery();
    }

    /**
     * Retrieve appointment records within a date range.
     */
    private function appointmentsBetween(Therapists $therapist, string $startDate, string $endDate): array
    {
        return $this->baseQuery($therapist)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))
            ->all();
    }

    /**
     * Format appointment details for the AI.
     */

    private function formatAppointment(UsersAppointments $appointment): array
    {
        $status = $appointment->status;

        if ($status instanceof BackedEnum) {
            $status = $status->value;
        }

        $service = $appointment->service;

        $customerName = $appointment->user?->name ?? ($appointment->walkInCustomer?->name ?? 'Guest customer');

        return [
            'customer' => $customerName,

            'service' => $service?->name ?? 'Service not specified',

            'service_description' => $service?->description,

            'duration_minutes' => $appointment->service_duration ?? $service?->duration_minutes,

            'add_on' => $appointment->addOn?->name,

            'date' => $appointment->appointment_date ? Carbon::parse($appointment->appointment_date, self::TIMEZONE)->format('l, F j, Y') : null,

            'date_iso' => $appointment->appointment_date ? Carbon::parse($appointment->appointment_date, self::TIMEZONE)->toDateString() : null,

            'start_time' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time, self::TIMEZONE)->format('g:i A') : 'Time not specified',

            'end_time' => $appointment->appointment_end_time ? Carbon::parse($appointment->appointment_end_time, self::TIMEZONE)->format('g:i A') : null,

            'status' => (string) ($status ?? 'unknown'),
        ];
    }
}
