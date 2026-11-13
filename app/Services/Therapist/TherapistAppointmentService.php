<?php

namespace App\Services\Therapist;

use App\Models\Services;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use BackedEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class TherapistAppointmentService
{
    private const TIMEZONE = 'Asia/Manila';

    private const EXCLUDED_STATUSES = [
        'cancelled',
        'rejected',
        'failed',
        'no show',
    ];

    private const WEEKDAYS = [
        'monday' => Carbon::MONDAY,
        'tuesday' => Carbon::TUESDAY,
        'wednesday' => Carbon::WEDNESDAY,
        'thursday' => Carbon::THURSDAY,
        'friday' => Carbon::FRIDAY,
        'saturday' => Carbon::SATURDAY,
        'sunday' => Carbon::SUNDAY,
    ];

    /**
     * Identify the therapist's request and return verified database context.
     */
    public function contextForQuestion(
        Therapists $therapist,
        string $question
    ): ?array {
        $question = $this->normalizeQuestionLanguage($question);
        $question = preg_replace(
            '/[^\p{L}\p{N}\s:\/-]/u',
            ' ',
            $question
        ) ?? $question;

        $question = preg_replace('/\s+/u', ' ', $question) ?? $question;

        if ($question === '') {
            return null;
        }
        if (preg_match('/\blater today\b/u', $question)) {
            return $this->getAppointmentsLaterToday($therapist);
        }

        /*
        |--------------------------------------------------------------------------
        | Monthly appointments
        |--------------------------------------------------------------------------
        */

        $hasExplicitDate = $this->extractSpecificDate($question) !== null;
        $hasMonthName = (bool) preg_match(
            '/\b(january|february|march|april|may|june|july|august|' .
                'september|october|november|december)\b/u',
            $question
        );

        $isMonthlyQuestion = (bool) preg_match(
            '/\b(this month|monthly|month schedule|monthly schedule|' .
                'monthly appointments|appointments this month|' .
                'appointments for this month|bookings this month|' .
                'bookings for this month|my month schedule|' .
                'my monthly schedule|rest of the month|' .
                'remaining days this month|next month|appointments next month|' .
                'appointments for next month|bookings next month|' .
                'schedule this month|schedule for this month|' .
                'schedule next month|schedule for next month|' .
                'how many.*this month|how many.*next month|' .
                'how busy.*this month|how busy.*next month|' .
                'clients this month|clients next month|' .
                'who are my clients this month|' .
                'who are my clients next month)\b/u',
            $question
        );

        if ($isMonthlyQuestion || ($hasMonthName && !$hasExplicitDate)) {
            $month = $this->extractMonth($question);

            if ($month === null) {
                $month = Carbon::now(self::TIMEZONE)->startOfMonth();

                if (preg_match('/\bnext month\b/u', $question)) {
                    $month->addMonthNoOverflow();
                }
            }

            $start = $month->copy()->startOfMonth()->toDateString();
            $end = $month->copy()->endOfMonth()->toDateString();

            $status = $this->detectStatus($question);

            if ($status !== null) {
                return $this->getAppointmentsByStatus(
                    $therapist,
                    $status,
                    $start,
                    $end
                );
            }

            if ($this->isWorkloadQuestion($question)) {
                return $this->getWorkloadForRange(
                    $therapist,
                    $start,
                    $end,
                    'monthly_workload',
                    $month->format('F Y')
                );
            }

            return $this->getMonthContext($therapist, $month);
        }

        /*
        |--------------------------------------------------------------------------
        | Status questions with date or period filters
        |--------------------------------------------------------------------------
        */

        $status = $this->detectStatus($question);

        if ($status !== null) {
            $range = $this->extractDateRange($question);

            if ($range !== null) {
                return $this->getAppointmentsByStatus(
                    $therapist,
                    $status,
                    $range['start'],
                    $range['end']
                );
            }

            $date = $this->extractSpecificDate($question);

            if ($date !== null) {
                return $this->getAppointmentsByStatus(
                    $therapist,
                    $status,
                    $date->toDateString(),
                    $date->toDateString()
                );
            }

            return $this->getAppointmentsByStatus($therapist, $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Next appointment
        |--------------------------------------------------------------------------
        */

        if (preg_match(
            '/\b(next appointment|next client|next customer|' .
                'next session|next booking|nearest appointment|' .
                'upcoming appointment|upcoming client|upcoming session|' .
                'when is my next|who is my next|what is my next|' .
                'what service is next|who am i seeing next)\b/u',
            $question
        )) {
            return $this->getNextAppointmentContext($therapist);
        }

        /*
        |--------------------------------------------------------------------------
        | Service catalog questions
        |--------------------------------------------------------------------------
        */

        if ($this->isServiceCatalogQuestion($question)) {
            return $this->getServiceCatalogContext($question);
        }

        /*
        |--------------------------------------------------------------------------
        | Free-time questions
        |--------------------------------------------------------------------------
        */

        if ($this->isFreeTimeQuestion($question)) {
            $range = $this->extractDateRange($question);

            if ($range !== null) {
                return $this->getFreeTimeForRange(
                    $therapist,
                    $range['start'],
                    $range['end']
                );
            }

            $date = $this->extractSpecificDate($question)
                ?? Carbon::now(self::TIMEZONE)->startOfDay();

            return $this->getFreeTimeContext(
                $therapist,
                $date->toDateString()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Workload questions
        |--------------------------------------------------------------------------
        */

        if ($this->isWorkloadQuestion($question)) {
            $range = $this->extractDateRange($question);

            if ($range !== null) {
                return $this->getWorkloadForRange(
                    $therapist,
                    $range['start'],
                    $range['end'],
                    $range['query']
                );
            }

            return $this->getWorkloadContext($therapist);
        }




        /*
|--------------------------------------------------------------------------
| Specific dates, weekdays, and time periods
|--------------------------------------------------------------------------
*/

        $specificDate = $this->extractSpecificDate($question);

        $timePeriod = $this->detectTimePeriod($question);

        if ($timePeriod !== null) {
            $date = $specificDate
                ?? Carbon::now(self::TIMEZONE)->startOfDay();

            return $this->getAppointmentsForTimePeriod(
                $therapist,
                $date->toDateString(),
                $timePeriod['start'],
                $timePeriod['end'],
                $timePeriod['period']
            );
        }

        if ($specificDate !== null) {
            return $this->getAppointmentsForDate(
                $therapist,
                $specificDate->toDateString()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Weekly schedules
        |--------------------------------------------------------------------------
        */

        if (preg_match(
            '/\b(this week|weekly|week schedule|schedule this week|' .
                'rest of the week|remaining days this week|next week|' .
                'last week|appointments.*this week|appointments.*next week|' .
                'appointments.*last week|schedule.*next week|' .
                'schedule.*last week)\b/u',
            $question
        )) {
            $range = $this->extractDateRange($question);

            if ($range !== null) {
                return $this->getAppointmentsForRange(
                    $therapist,
                    $range['start'],
                    $range['end'],
                    $range['query']
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tomorrow
        |--------------------------------------------------------------------------
        */

        if (preg_match('/\b(tomorrow|tomorrows|next day)\b/u', $question)) {
            return $this->getTomorrowContext($therapist);
        }

        /*
        |--------------------------------------------------------------------------
        | Today
        |--------------------------------------------------------------------------
        */

        if (
            preg_match('/\b(today|todays|tonight|this day)\b/u', $question) ||
            preg_match(
                '/\b(who am i seeing|what do i have|am i working|' .
                    'am i busy|how many do i have|do i have any|' .
                    'how many appointments|how many clients|' .
                    'how many bookings|what is my schedule|' .
                    'whats my schedule)\b/u',
                $question
            ) ||
            preg_match(
                '/^(my )?(schedule|appointments|bookings|clients|' .
                    'customers|sessions)( today)?$/u',
                $question
            )
        ) {
            return $this->getTodayContext($therapist);
        }

        return null;
    }


    /**
     * Resolve appointment context using the current question
     * and relevant previous user messages.
     */
    public function contextForConversation(
        Therapists $therapist,
        array $messages
    ): ?array {
        $userMessages = collect($messages)
            ->where('role', 'user')
            ->pluck('content')
            ->map(fn($content) => trim((string) $content))
            ->filter()
            ->values()
            ->all();

        if ($userMessages === []) {
            return null;
        }

        $latestQuestion = $userMessages[array_key_last($userMessages)];

        // An explicit date or period in the latest question takes priority.
        if (
            $this->hasDateReference($latestQuestion) ||
            !$this->isFollowUpQuestion($latestQuestion) ||
            count($userMessages) < 2
        ) {
            return $this->contextForQuestion($therapist, $latestQuestion);
        }

        /*
     * Include up to three previous user questions.
     * This allows multiple consecutive follow-ups to retain context.
     */
        $previousQuestions = array_slice(
            $userMessages,
            -4,
            3
        );

        $combinedQuestion = implode(' ', $previousQuestions)
            . ' '
            . $latestQuestion;

        return $this->contextForQuestion(
            $therapist,
            $combinedQuestion
        );
    }

    /**
     * Determine whether the question contains an explicit date or period.
     */
    private function hasDateReference(string $question): bool
    {
        $question = $this->normalizeQuestionLanguage($question);
        $question = mb_strtolower(trim($question));
        return $this->extractSpecificDate($question) !== null
            || $this->extractDateRange($question) !== null
            || (bool) preg_match(
                '/\b(today|tomorrow|tomorrows|tonight|yesterday|' .
                    'this day|next day|this week|next week|last week|' .
                    'this month|next month|last month|monthly|weekly|' .
                    'january|february|march|april|may|june|july|august|' .
                    'september|october|november|december)\b/u',
                $question
            );
    }

    /**
     * Recognize short follow-up questions that need previous context.
     */
    private function isFollowUpQuestion(string $question): bool
    {
        $question = mb_strtolower(trim($question));

        return (bool) preg_match(
            '/\b(what about|how about|and what|and who|and how|' .
                'who else|what else|how many|which ones|list them|' .
                'show them|tell me more|more details|sino naman|' .
                'ano naman|ilan naman|paano naman|sino pa|' .
                'ano pa|alin dito)\b/u',
            $question
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Daily, weekly, and monthly appointment contexts
    |--------------------------------------------------------------------------
    */

    public function getTodayContext(Therapists $therapist): array
    {
        $today = Carbon::now(self::TIMEZONE)->startOfDay();

        $records = $this->appointmentsBetween(
            $therapist,
            $today->toDateString(),
            $today->toDateString()
        );

        return array_merge([
            'query' => 'today',
            'date' => $today->format('F j, Y'),
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    public function getTomorrowContext(Therapists $therapist): array
    {
        $tomorrow = Carbon::now(self::TIMEZONE)->addDay()->startOfDay();

        $records = $this->appointmentsBetween(
            $therapist,
            $tomorrow->toDateString(),
            $tomorrow->toDateString()
        );

        return array_merge([
            'query' => 'tomorrow',
            'date' => $tomorrow->format('F j, Y'),
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    public function getWeekContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);

        return $this->getAppointmentsForRange(
            $therapist,
            $start->toDateString(),
            $end->toDateString(),
            'this_week'
        );
    }

    public function getNextWeekContext(Therapists $therapist): array
    {
        $start = Carbon::now(self::TIMEZONE)
            ->addWeek()
            ->startOfWeek(Carbon::MONDAY);

        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        return $this->getAppointmentsForRange(
            $therapist,
            $start->toDateString(),
            $end->toDateString(),
            'next_week'
        );
    }

    public function getMonthContext(
        Therapists $therapist,
        ?Carbon $month = null
    ): array {
        $month = ($month ?? Carbon::now(self::TIMEZONE))->copy();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $records = $this->appointmentsBetween(
            $therapist,
            $start->toDateString(),
            $end->toDateString()
        );

        return array_merge([
            'query' => 'monthly',
            'month' => $month->format('F Y'),
            'date_range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    public function getAppointmentsForDate(
        Therapists $therapist,
        string $date
    ): array {
        $day = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        return $this->getAppointmentsForRange(
            $therapist,
            $day->toDateString(),
            $day->toDateString(),
            'specific_date',
            $day->format('l, F j, Y')
        );
    }
    public function getAppointmentsLaterToday(
        Therapists $therapist
    ): array {
        $now = Carbon::now(self::TIMEZONE);

        $records = $this->baseQuery($therapist)
            ->whereDate('appointment_date', $now->toDateString())
            ->whereTime('appointment_time', '>=', $now->format('H:i:s'))
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->orderBy('appointment_time')
            ->get()
            ->map(
                fn(UsersAppointments $appointment) =>
                $this->formatAppointment($appointment)
            )
            ->all();

        return array_merge([
            'query' => 'appointments_later_today',
            'time_period' => 'later_today',
            'date' => $now->format('l, F j, Y'),
            'time_range' => [
                'start' => $now->format('H:i:s'),
                'end' => '23:59:59',
            ],
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    public function getAppointmentsForTimePeriod(
        Therapists $therapist,
        string $date,
        string $startTime,
        string $endTime,
        string $period
    ): array {
        $day = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        $records = $this->baseQuery($therapist)
            ->whereDate('appointment_date', $day->toDateString())
            ->whereTime('appointment_time', '>=', $startTime)
            ->whereTime('appointment_time', '<=', $endTime)
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->orderBy('appointment_time')
            ->get()
            ->map(
                fn(UsersAppointments $appointment) =>
                $this->formatAppointment($appointment)
            )
            ->all();

        return array_merge([
            'query' => 'appointments_by_time_period',
            'time_period' => $period,
            'date' => $day->format('l, F j, Y'),
            'time_range' => [
                'start' => $startTime,
                'end' => $endTime,
            ],
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }


    public function getAppointmentsForRange(
        Therapists $therapist,
        string $startDate,
        string $endDate,
        string $query = 'date_range',
        ?string $label = null
    ): array {
        $records = $this->appointmentsBetween(
            $therapist,
            $startDate,
            $endDate
        );

        return array_merge([
            'query' => $query,
            'label' => $label,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment status queries
    |--------------------------------------------------------------------------
    */

    public function getAppointmentsByStatus(
        Therapists $therapist,
        string $status,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $now = Carbon::now(self::TIMEZONE);
        $databaseStatus = $this->databaseStatusValue($status);

        $query = $this->baseQuery($therapist)
            ->where('status', $databaseStatus);

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('appointment_date', [
                $startDate,
                $endDate,
            ]);
        } elseif (in_array($status, ['pending', 'confirmed'], true)) {
            $query->whereDate(
                'appointment_date',
                '>=',
                $now->toDateString()
            );
        }

        $appointments = $query
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $records = $appointments
            ->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))
            ->all();

        return array_merge([
            'query' => 'appointments_by_status',
            'requested_status' => $status,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    private function databaseStatusValue(string $status): string
    {
        return match ($status) {
            'confirmed' => 'confirm',
            'no_show' => 'no show',
            default => $status,
        };
    }

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

    /*
    |--------------------------------------------------------------------------
    | Workload analytics
    |--------------------------------------------------------------------------
    */

    public function getWorkloadContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);

        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);

        return $this->getWorkloadForRange(
            $therapist,
            $start->toDateString(),
            $end->toDateString(),
            'workload'
        );
    }

    public function getWorkloadForRange(
        Therapists $therapist,
        string $startDate,
        string $endDate,
        string $query = 'workload',
        ?string $label = null
    ): array {
        $start = Carbon::parse($startDate, self::TIMEZONE)->startOfDay();
        $end = Carbon::parse($endDate, self::TIMEZONE)->startOfDay();

        $records = $this->appointmentsBetween(
            $therapist,
            $start->toDateString(),
            $end->toDateString()
        );

        $scheduledRecords = array_values(array_filter(
            $records,
            fn(array $record): bool => !in_array(
                $this->normalizeStatus($record['status'] ?? 'unknown'),
                self::EXCLUDED_STATUSES,
                true
            )
        ));

        $dailyCounts = [];

        // Include days with zero appointments as well.
        for (
            $day = $start->copy();
            $day->lessThanOrEqualTo($end);
            $day->addDay()
        ) {
            $date = $day->toDateString();

            $dailyCounts[$date] = [
                'date' => $date,
                'day' => $day->format('l'),
                'appointments' => 0,
            ];
        }

        foreach ($scheduledRecords as $record) {
            $date = $record['date_iso'] ?? null;

            if ($date !== null && isset($dailyCounts[$date])) {
                $dailyCounts[$date]['appointments']++;
            }
        }

        $days = array_values($dailyCounts);

        usort(
            $days,
            fn(array $a, array $b) =>
            $b['appointments'] <=> $a['appointments']
        );

        $highest = $days[0]['appointments'] ?? 0;

        $busiestDays = $highest > 0
            ? array_values(array_filter(
                $days,
                fn(array $day) => $day['appointments'] === $highest
            ))
            : [];

        $lowest = $days
            ? min(array_column($days, 'appointments'))
            : 0;

        $leastBusyDays = array_values(array_filter(
            $days,
            fn(array $day) => $day['appointments'] === $lowest
        ));

        return [
            'query' => $query,
            'label' => $label,
            'date_range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'timezone' => self::TIMEZONE,
            'total_appointments' => count($scheduledRecords),
            'days_in_period' => count($days),
            'days_with_appointments' => count(array_filter(
                $days,
                fn(array $day) => $day['appointments'] > 0
            )),
            'busiest_day_count' => $highest,
            'busiest_days' => $busiestDays,
            'least_busy_day_count' => $lowest,
            'least_busy_days' => $leastBusyDays,
            'daily_counts' => $days,
            'status_counts' => $this->summarizeAppointments($records)['status_counts'],
            'note' => 'Workload totals exclude cancelled, rejected, failed, and no-show appointments.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Free-time gaps
    |--------------------------------------------------------------------------
    */

    private function isFreeTimeQuestion(string $question): bool
    {
        return (bool) preg_match(
            '/\b(free time|free slot|free slots|gaps between|' .
                'available time|when am i free|when am i available|' .
                'am i free|do i have free time|any free time|' .
                'any openings|open slot|open slots)\b/u',
            $question
        );
    }

    public function getFreeTimeContext(
        Therapists $therapist,
        string $date
    ): array {
        $day = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        $appointments = $this->baseQuery($therapist)
            ->whereDate('appointment_date', $day->toDateString())
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->orderBy('appointment_time')
            ->get()
            ->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))
            ->filter(
                fn(array $record) =>
                !empty($record['start_time']) &&
                    !empty($record['end_time']) &&
                    $record['start_time'] !== 'Time not specified'
            )
            ->values()
            ->all();

        $gaps = $this->calculateGaps($appointments, $day);

        return [
            'query' => 'free_time_gaps',
            'date' => $day->format('l, F j, Y'),
            'date_range' => [
                'start' => $day->toDateString(),
                'end' => $day->toDateString(),
            ],
            'timezone' => self::TIMEZONE,
            'total_appointments' => count($appointments),
            'total_gaps' => count($gaps),
            'free_time_gaps' => $gaps,
            'note' => 'These are gaps between scheduled appointments, not guaranteed availability outside working hours.',
        ];
    }

    public function getFreeTimeForRange(
        Therapists $therapist,
        string $startDate,
        string $endDate
    ): array {
        $start = Carbon::parse($startDate, self::TIMEZONE)->startOfDay();
        $end = Carbon::parse($endDate, self::TIMEZONE)->startOfDay();

        $days = [];
        $totalAppointments = 0;
        $totalGaps = 0;

        for (
            $day = $start->copy();
            $day->lessThanOrEqualTo($end);
            $day->addDay()
        ) {
            $context = $this->getFreeTimeContext(
                $therapist,
                $day->toDateString()
            );

            $days[] = [
                'date' => $day->toDateString(),
                'day' => $day->format('l'),
                'total_appointments' => $context['total_appointments'],
                'total_gaps' => $context['total_gaps'],
                'free_time_gaps' => $context['free_time_gaps'],
            ];

            $totalAppointments += $context['total_appointments'];
            $totalGaps += $context['total_gaps'];
        }

        return [
            'query' => 'free_time_range',
            'date_range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'timezone' => self::TIMEZONE,
            'total_appointments' => $totalAppointments,
            'total_gaps' => $totalGaps,
            'days' => $days,
            'note' => 'These are gaps between scheduled appointments, not guaranteed availability outside working hours.',
        ];
    }

    private function calculateGaps(array $appointments, Carbon $day): array
    {
        $gaps = [];
        $previousEnd = null;

        foreach ($appointments as $appointment) {
            $start = Carbon::parse(
                $day->toDateString() . ' ' . $appointment['start_time'],
                self::TIMEZONE
            );

            $end = Carbon::parse(
                $day->toDateString() . ' ' . $appointment['end_time'],
                self::TIMEZONE
            );

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            if (
                $previousEnd !== null &&
                $start->greaterThan($previousEnd)
            ) {
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

        return $gaps;
    }

    /*
    |--------------------------------------------------------------------------
    | Service catalog
    |--------------------------------------------------------------------------
    */

    private function isServiceCatalogQuestion(string $question): bool
    {
        return (bool) preg_match(
            '/\b(what services|which services|list.*services|' .
                'available services|massage services|service catalog|' .
                'services offered|tell me about.*service|' .
                'describe.*service|service description|service details|' .
                'how long is.*massage|how long does.*massage|' .
                'duration of.*massage|price of.*massage|' .
                'cost of.*massage|how much is.*massage|' .
                'how much does.*massage|what is.*massage|' .
                'what does.*massage include|massage duration|' .
                'service duration|service price)\b/u',
            $question
        );
    }

    public function getServiceCatalogContext(string $question): array
    {
        $query = Services::query();

        $searchTerms = $this->extractServiceSearchTerms($question);

        if ($searchTerms !== []) {
            $query->where(function (Builder $builder) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $builder->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                }
            });
        }

        $services = $query
            ->orderBy('name')
            ->limit(30)
            ->get([
                'id',
                'name',
                'description',
                'duration_minutes',
                'price',
                'status',
            ]);

        $records = $services->map(fn(Services $service) => [
            'name' => $service->name,
            'description' => $service->description,
            'duration_minutes' => $service->duration_minutes,
            'duration' => $service->duration_minutes !== null
                ? $this->formatDuration((int) $service->duration_minutes)
                : null,
            'price' => $service->price,
            'status' => $service->status,
        ])->all();

        return [
            'query' => 'service_catalog',
            'search_terms' => $searchTerms,
            'total_services' => count($records),
            'services' => $records,
            'note' => 'Use only the service records supplied here. Do not assume a service is bookable based only on its name.',
        ];
    }

    private function extractServiceSearchTerms(string $question): array
    {
        $question = preg_replace(
            '/\b(what|which|are|is|the|a|an|about|tell|me|list|show|' .
                'all|available|offered|services|service|catalog|massage|' .
                'how|long|does|do|duration|take|takes|price|cost|much|' .
                'include|includes|describe|description|details|of|for|' .
                'we|you|have|offer|can|i|get|please|and|my|your)\b/u',
            ' ',
            $question
        ) ?? $question;

        $terms = preg_split('/\s+/u', trim($question)) ?: [];

        return array_values(array_unique(array_filter(
            $terms,
            fn(string $term) => mb_strlen($term) >= 3
        )));
    }

    /*
    |--------------------------------------------------------------------------
    | Next appointment
    |--------------------------------------------------------------------------
    */

    public function getNextAppointmentContext(Therapists $therapist): array
    {
        $now = Carbon::now(self::TIMEZONE);
        $today = $now->toDateString();

        $appointment = $this->baseQuery($therapist)
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->where(function (Builder $query) use ($today, $now) {
                $query->whereDate('appointment_date', '>', $today)
                    ->orWhere(function (Builder $todayQuery) use ($today, $now) {
                        $todayQuery->whereDate('appointment_date', $today)
                            ->where(
                                'appointment_time',
                                '>=',
                                $now->format('H:i:s')
                            );
                    });
            })
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $records = $appointment
            ? [$this->formatAppointment($appointment)]
            : [];

        return array_merge([
            'query' => 'next_appointment',
            'date' => $now->format('F j, Y'),
            'timezone' => self::TIMEZONE,
        ], $this->summarizeAppointments($records));
    }

    /*
    |--------------------------------------------------------------------------
    | Date and period extraction
    |--------------------------------------------------------------------------
    */

    private function extractDateRange(string $question): ?array
    {
        $now = Carbon::now(self::TIMEZONE);

        if (preg_match('/\b(last week|previous week)\b/u', $question)) {
            $start = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

            return $this->dateRange($start, $end, 'last_week');
        }

        if (preg_match('/\b(next week)\b/u', $question)) {
            $start = $now->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

            return $this->dateRange($start, $end, 'next_week');
        }

        if (preg_match('/\b(this week|weekly|week schedule)\b/u', $question)) {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);
            $end = $now->copy()->endOfWeek(Carbon::SUNDAY);

            return $this->dateRange($start, $end, 'this_week');
        }

        if (preg_match('/\b(last month|previous month)\b/u', $question)) {
            $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return $this->dateRange($start, $end, 'last_month');
        }

        if (preg_match('/\b(next month)\b/u', $question)) {
            $start = $now->copy()->addMonthNoOverflow()->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return $this->dateRange($start, $end, 'next_month');
        }

        if (preg_match('/\b(this month|monthly)\b/u', $question)) {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();

            return $this->dateRange($start, $end, 'this_month');
        }

        return null;
    }

    private function dateRange(
        Carbon $start,
        Carbon $end,
        string $query
    ): array {
        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'query' => $query,
        ];
    }

    private function extractMonth(string $question): ?Carbon
    {
        $months = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december',
        ];

        $pattern = '/\b(' . implode('|', $months) . ')\b(?:\s+(\d{4}))?/u';

        if (!preg_match($pattern, $question, $matches)) {
            return null;
        }

        $year = isset($matches[2])
            ? (int) $matches[2]
            : Carbon::now(self::TIMEZONE)->year;

        try {
            return Carbon::createFromFormat(
                '!F Y',
                ucfirst($matches[1]) . ' ' . $year,
                self::TIMEZONE
            );
        } catch (Throwable) {
            return null;
        }
    }

    private function extractSpecificDate(string $question): ?Carbon
    {
        $now = Carbon::now(self::TIMEZONE);

        // Relative day words.
        if (preg_match('/\b(today|todays|tonight|this day)\b/u', $question)) {
            return $now->copy()->startOfDay();
        }

        if (preg_match('/\b(tomorrow|tomorrows|next day)\b/u', $question)) {
            return $now->copy()->addDay()->startOfDay();
        }

        // "This Friday" means Friday in the current Monday-Sunday week.
        if (preg_match(
            '/\b(this|next)\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/u',
            $question,
            $matches
        )) {
            $date = $now->copy()->startOfWeek(Carbon::MONDAY);

            if ($matches[1] === 'next') {
                $date->addWeek();
            }

            return $date
                ->addDays(self::WEEKDAYS[$matches[2]] - Carbon::MONDAY)
                ->startOfDay();
        }

        // A bare weekday means its next occurrence, including today.
        if (preg_match(
            '/\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/u',
            $question,
            $matches
        )) {
            $weekday = self::WEEKDAYS[$matches[1]];
            $daysAhead = ($weekday - $now->dayOfWeekIso + 7) % 7;

            return $now->copy()->startOfDay()->addDays($daysAhead);
        }

        $patterns = [
            '/\b(\d{4}-\d{2}-\d{2})\b/u',
            '/\b(\d{1,2}\/\d{1,2}\/\d{2,4})\b/u',
            '/\b((?:january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{1,2}(?:,?\s+\d{4})?)\b/u',
            '/\b(\d{1,2}\s+(?:january|february|march|april|may|june|july|august|september|october|november|december)(?:\s+\d{4})?)\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (!preg_match($pattern, $question, $matches)) {
                continue;
            }

            try {
                $date = Carbon::parse($matches[1], self::TIMEZONE);

                // Reject dates that Carbon silently normalizes, such as February 31.
                if (
                    preg_match('/^\d{4}-\d{2}-\d{2}$/', $matches[1]) &&
                    $date->format('Y-m-d') !== $matches[1]
                ) {
                    return null;
                }

                return $date->startOfDay();
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }


    /**
     * Normalize common Tagalog expressions into English
     * so the existing appointment detection can understand them.
     */
    private function normalizeQuestionLanguage(string $question): string
    {
        $question = mb_strtolower(trim($question));

        $translations = [
            // Monthly periods.
            'sa susunod na buwan' => 'next month',
            'susunod na buwan' => 'next month',
            'ngayong buwan' => 'this month',
            'buwan na ito' => 'this month',
            'nakaraang buwan' => 'last month',

            // Weekly periods.
            'sa linggong ito' => 'this week',
            'linggong ito' => 'this week',
            'susunod na linggo' => 'next week',
            'nakaraang linggo' => 'last week',

            // Relative dates.
            'ngayong araw' => 'today',
            'araw na ito' => 'today',
            'sa susunod na araw' => 'tomorrow',

            // Next appointment questions.
            'susunod na appointment' => 'next appointment',
            'susunod na customer' => 'next customer',
            'susunod na kliyente' => 'next client',

            // Question words.
            'ano ang' => 'what is',
            'sino ang' => 'who is',
            'ilan ang' => 'how many',
            'ilan' => 'how many',
            'ano' => 'what',
            'sino' => 'who',
            'may' => 'do I have an ',

            // Weekdays.
            'lunes' => 'monday',
            'martes' => 'tuesday',
            'miyerkules' => 'wednesday',
            'huwebes' => 'thursday',
            'biyernes' => 'friday',
            'sabado' => 'saturday',

            // Sunday when used as a day.
            'sa linggo' => 'on sunday',

            // Relative days.
            'ngayon' => 'today',
            'bukas' => 'tomorrow',
            'kahapon' => 'yesterday',


            // Time of day.
            'mamayang umaga' => 'this morning',
            'mamayang hapon' => 'this afternoon',
            'mamayang gabi' => 'this evening',
            'sa umaga' => 'in the morning',
            'sa hapon' => 'in the afternoon',
            'sa gabi' => 'in the evening',
            'mamaya' => 'later today',
        ];

        // Replace longer phrases before shorter expressions.
        uksort(
            $translations,
            fn(string $a, string $b) =>
            mb_strlen($b) <=> mb_strlen($a)
        );

        foreach ($translations as $tagalog => $english) {
            $question = preg_replace(
                '/(?<!\p{L})' . preg_quote($tagalog, '/') . '(?!\p{L})/u',
                $english,
                $question
            ) ?? $question;
        }

        return $question;
    }


    /*
    |--------------------------------------------------------------------------
    | Intent helpers
    |--------------------------------------------------------------------------
    */


    /**
     * Detect a requested time period within a day.
     */
    private function detectTimePeriod(string $question): ?array
    {
        $patterns = [
            'morning' => [
                '/\b(morning|this morning|mamayang umaga|sa umaga)\b/u',
                'start' => '05:00:00',
                'end' => '11:59:59',
            ],
            'afternoon' => [
                '/\b(afternoon|this afternoon|mamayang hapon|sa hapon)\b/u',
                'start' => '12:00:00',
                'end' => '16:59:59',
            ],
            'evening' => [
                '/\b(evening|this evening|tonight|mamayang gabi|sa gabi)\b/u',
                'start' => '17:00:00',
                'end' => '23:59:59',
            ],
            'late_night' => [
                '/\b(late night|late tonight|hatinggabi)\b/u',
                'start' => '00:00:00',
                'end' => '04:59:59',
            ],
        ];

        foreach ($patterns as $period => $pattern) {
            if (preg_match($pattern[0], $question)) {
                return [
                    'period' => $period,
                    'start' => $pattern['start'],
                    'end' => $pattern['end'],
                ];
            }
        }

        return null;
    }


    private function isWorkloadQuestion(string $question): bool
    {
        return (bool) preg_match(
            '/\b(workload|busiest day|busiest|least busy|' .
                'how busy|appointments per day|sessions per day|' .
                'most appointments|least appointments|' .
                'days with the most|days with the least)\b/u',
            $question
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment summaries and formatting
    |--------------------------------------------------------------------------
    */

    private function summarizeAppointments(array $records): array
    {
        $statuses = [];

        foreach ($records as $record) {
            $status = $this->normalizeStatus($record['status'] ?? 'unknown');

            if ($status === 'confirm') {
                $status = 'confirmed';
            }

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

    private function normalizeStatus(mixed $status): string
    {
        if ($status instanceof BackedEnum) {
            $status = $status->value;
        }

        return strtolower(trim((string) $status));
    }

    private function formatAppointment(
        UsersAppointments $appointment
    ): array {
        $status = $this->normalizeStatus($appointment->status);
        $service = $appointment->service;

        $customerName = $appointment->user?->name
            ?? $appointment->walkInCustomer?->name
            ?? 'Guest customer';

        $date = $appointment->appointment_date
            ? Carbon::parse(
                $appointment->appointment_date,
                self::TIMEZONE
            )
            : null;

        return [
            'customer' => $customerName,
            'service' => $service?->name ?? 'Service not specified',
            'service_description' => $service?->description,
            'duration_minutes' => $appointment->service_duration
                ?? $service?->duration_minutes,
            'add_on' => $appointment->addOn?->name,
            'date' => $date?->format('l, F j, Y'),
            'date_iso' => $date?->toDateString(),
            'start_time' => $appointment->appointment_time
                ? Carbon::parse(
                    $appointment->appointment_time,
                    self::TIMEZONE
                )->format('g:i A')
                : 'Time not specified',
            'end_time' => $appointment->appointment_end_time
                ? Carbon::parse(
                    $appointment->appointment_end_time,
                    self::TIMEZONE
                )->format('g:i A')
                : null,
            'status' => $status,
        ];
    }

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

    /*
    |--------------------------------------------------------------------------
    | Database query helpers
    |--------------------------------------------------------------------------
    */

    private function baseQuery(Therapists $therapist): Builder
    {
        return $therapist
            ->appointments()
            ->with(['user', 'walkInCustomer', 'service', 'addOn'])
            ->getQuery();
    }

    private function appointmentsBetween(
        Therapists $therapist,
        string $startDate,
        string $endDate
    ): array {
        return $this->baseQuery($therapist)
            ->whereBetween('appointment_date', [
                $startDate,
                $endDate,
            ])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->map(fn(UsersAppointments $appointment) => $this->formatAppointment($appointment))
            ->all();
    }
}
