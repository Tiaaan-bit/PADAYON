<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAiAnalyticsService
{
    private const TIMEZONE = 'Asia/Manila';

    private const CONFIRMED_STATUS = 'confirm';

    private const WORKING_HOURS_PER_DAY = 12;

    /**
     * Build analytics for the question being asked.
     */
    public function contextForQuestion(string $question): array
    {
        [$start, $end, $periodLabel] = $this->resolvePeriod($question);

        $numberOfDays = (int) $start->diffInDays($end) + 1;

        $previousEnd = $start->copy()->subDay()->endOfDay();

        $previousStart = $previousEnd->copy()
            ->subDays($numberOfDays - 1)
            ->startOfDay();

        $current = $this->periodMetrics($start, $end);
        $previous = $this->periodMetrics($previousStart, $previousEnd);

        return [
            'timezone' => self::TIMEZONE,
            'requested_question' => $question,

            'period' => [
                'label' => $periodLabel,
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'days' => $numberOfDays,
            ],

            'comparison_period' => [
                'start' => $previousStart->toDateString(),
                'end' => $previousEnd->toDateString(),
            ],

            'current' => $current,
            'previous' => $previous,

            'comparison' => [
                'revenue' => $this->comparison(
                    $previous['recorded_revenue'],
                    $current['recorded_revenue']
                ),

                'appointments' => $this->comparison(
                    $previous['appointment_count'],
                    $current['appointment_count']
                ),

                'average_revenue_per_appointment' => $this->comparison(
                    $previous['average_revenue_per_appointment'],
                    $current['average_revenue_per_appointment']
                ),
            ],

            'all_time_reference' => [
                'services_count' => DB::table('services')->count(),
                'therapists_count' => DB::table('therapists')->count(),
                'registered_customers_count' => DB::table('users')
                    ->where('role', 'user')
                    ->count(),
                'walk_in_customers_count' => DB::table('walk_in_customers')
                    ->count(),
            ],

            'definitions' => [
                'recorded_revenue' =>
                'Sum of amount_paid for confirmed appointments scheduled within the selected period. Grouped by appointment date, not payment transaction date.',

                'appointment_value' =>
                'Sum of service_price and addons_price. This is the total charge for an appointment.',

                'outstanding_balance' =>
                'Appointment value minus amount_paid, with negative balances treated as zero.',

                'payment_status' =>
                'The stored payment_status field. A paid downpayment can still have an outstanding balance.',

                'payment_history_limitation' =>
                'Individual installment transactions are not stored separately. Historical installment amounts and exact collection dates cannot be reconstructed reliably.',

                'customer_analytics' =>
                'Customers are identified using user_id or walk_in_customer_id. Walk-in records linked to registered users are combined under the registered user identity.',

                'returning_customers' =>
                'Customers with at least one confirmed appointment before the selected period, or more than one confirmed appointment by the end of the selected period.',

                'therapist_utilization' =>
                'Estimated booked appointment minutes divided by 720 available minutes per therapist per day. Assumes every therapist works daily from 1:00 PM to 1:00 AM.',

                'profit_limitation' =>
                'Operating expenses are not recorded in the available database. Net profit cannot be calculated.',

                'comparison' =>
                'Compared with the immediately preceding period containing the same number of calendar days.',
            ],
        ];
    }

    /**
     * Collect all metrics for a reporting period.
     */
    private function periodMetrics(Carbon $start, Carbon $end): array
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $base = DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        $confirmed = (clone $base)
            ->where('status', self::CONFIRMED_STATUS);

        $appointmentCount = (clone $confirmed)->count();

        $recordedRevenue = (float) (clone $confirmed)
            ->sum('amount_paid');

        $totalAppointmentValue = (float) (clone $confirmed)
            ->sum(DB::raw(
                'COALESCE(service_price, 0) + COALESCE(addons_price, 0)'
            ));

        $averageRevenue = $appointmentCount > 0
            ? $recordedRevenue / $appointmentCount
            : 0;

        $statusBreakdown = $this->statusBreakdown(clone $base);

        $servicePerformance = $this->servicePerformance(
            $startDate,
            $endDate
        );

        $paymentAnalytics = $this->paymentAnalytics(
            $startDate,
            $endDate
        );

        $therapistAnalytics = $this->therapistAnalytics(
            $startDate,
            $endDate,
            $start,
            $end
        );

        $customerAnalytics = $this->customerAnalytics(
            $startDate,
            $endDate
        );

        $appointmentDemand = $this->appointmentDemand(
            $startDate,
            $endDate
        );

        $dailyRevenue = $this->dailyRevenue(
            $startDate,
            $endDate
        );

        $cancelledCount = $statusBreakdown['cancelled'] ?? 0;

        $totalAppointments = array_sum($statusBreakdown);

        $cancellationRate = $totalAppointments > 0
            ? round(($cancelledCount / $totalAppointments) * 100, 2)
            : 0;

        return [
            'recorded_revenue' => round($recordedRevenue, 2),

            'total_appointment_value' => round(
                $totalAppointmentValue,
                2
            ),

            'average_revenue_per_appointment' => round(
                $averageRevenue,
                2
            ),

            'appointment_count' => $appointmentCount,

            'total_appointments' => $totalAppointments,

            'cancellation_rate' => $cancellationRate,

            'status_breakdown' => $statusBreakdown,

            'services' => $servicePerformance,

            'payments' => $paymentAnalytics,

            'therapists' => $therapistAnalytics,

            'customers' => $customerAnalytics,

            'appointment_demand' => $appointmentDemand,

            'daily_revenue' => $dailyRevenue,

            'financial_limitations' => [
                'expenses_available' => false,
                'net_profit_available' => false,
                'individual_payment_history_available' => false,
            ],
        ];
    }

    /**
     * Appointment status breakdown.
     */
    private function statusBreakdown($base): array
    {
        $rows = $base
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $statuses = [];

        foreach ($rows as $row) {
            $statuses[$row->status ?? 'unknown'] = (int) $row->total;
        }

        return $statuses;
    }

    /**
     * Service popularity and revenue.
     */
    private function servicePerformance(
        string $startDate,
        string $endDate
    ): array {
        return DB::table('services')
            ->leftJoin('appointments', function ($join) use (
                $startDate,
                $endDate
            ) {
                $join->on(
                    'services.id',
                    '=',
                    'appointments.service_id'
                )
                    ->whereBetween(
                        'appointments.appointment_date',
                        [$startDate, $endDate]
                    )
                    ->where(
                        'appointments.status',
                        self::CONFIRMED_STATUS
                    );
            })
            ->select(
                'services.id',
                'services.name',
                DB::raw('COUNT(appointments.id) as appointments'),
                DB::raw(
                    'COALESCE(SUM(appointments.service_price), 0) as service_revenue'
                ),
                DB::raw(
                    'COALESCE(SUM(appointments.addons_price), 0) as addon_revenue'
                ),
                DB::raw(
                    'COALESCE(SUM(appointments.amount_paid), 0) as recorded_revenue'
                )
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('appointments')
            ->get()
            ->map(fn($row) => [
                'service' => $row->name,
                'appointments' => (int) $row->appointments,
                'service_revenue' => round((float) $row->service_revenue, 2),
                'addon_revenue' => round((float) $row->addon_revenue, 2),
                'recorded_revenue' => round((float) $row->recorded_revenue, 2),
            ])
            ->values()
            ->all();
    }

    /**
     * Payment methods, types, statuses and outstanding balances.
     */
    private function paymentAnalytics(
        string $startDate,
        string $endDate
    ): array {
        $confirmed = DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', self::CONFIRMED_STATUS);

        $paymentMethods = (clone $confirmed)
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as appointments'),
                DB::raw('SUM(COALESCE(amount_paid, 0)) as recorded_revenue')
            )
            ->groupBy('payment_method')
            ->orderByDesc('recorded_revenue')
            ->get()
            ->map(fn($row) => [
                'payment_method' => $row->payment_method ?? 'Unknown',
                'appointments' => (int) $row->appointments,
                'recorded_revenue' => round(
                    (float) $row->recorded_revenue,
                    2
                ),
            ])
            ->values()
            ->all();

        $paymentTypes = (clone $confirmed)
            ->select(
                'payment_type',
                DB::raw('COUNT(*) as appointments'),
                DB::raw('SUM(COALESCE(amount_paid, 0)) as recorded_revenue')
            )
            ->groupBy('payment_type')
            ->get()
            ->map(fn($row) => [
                'payment_type' => $row->payment_type ?? 'Unknown',
                'appointments' => (int) $row->appointments,
                'recorded_revenue' => round(
                    (float) $row->recorded_revenue,
                    2
                ),
            ])
            ->values()
            ->all();

        $paymentStatuses = DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->select(
                'payment_status',
                DB::raw('COUNT(*) as appointments')
            )
            ->groupBy('payment_status')
            ->get()
            ->map(fn($row) => [
                'payment_status' => $row->payment_status ?? 'Unknown',
                'appointments' => (int) $row->appointments,
            ])
            ->values()
            ->all();

        $balance = (clone $confirmed)
            ->selectRaw('
                COUNT(*) as appointments,
                SUM(
                    GREATEST(
                        0,
                        COALESCE(service_price, 0)
                        + COALESCE(addons_price, 0)
                        - COALESCE(amount_paid, 0)
                    )
                ) as outstanding_balance,
                SUM(
                    CASE
                        WHEN (
                            COALESCE(service_price, 0)
                            + COALESCE(addons_price, 0)
                            - COALESCE(amount_paid, 0)
                        ) > 0
                        THEN 1 ELSE 0
                    END
                ) as appointments_with_balance
            ')
            ->first();

        return [
            'payment_methods' => $paymentMethods,

            'payment_types' => $paymentTypes,

            'payment_statuses' => $paymentStatuses,

            'outstanding_balances' => [
                'appointments_with_balance' => (int) (
                    $balance->appointments_with_balance ?? 0
                ),

                'total_balance' => round(
                    (float) ($balance->outstanding_balance ?? 0),
                    2
                ),
            ],
        ];
    }

    /**
     * Therapist workload and estimated utilization.
     *
     * Assumes every therapist works 12 hours every day.
     */
    private function therapistAnalytics(
        string $startDate,
        string $endDate,
        Carbon $start,
        Carbon $end
    ): array {
        $days = (int) $start->diffInDays($end) + 1;

        $availableMinutesPerTherapist =
            self::WORKING_HOURS_PER_DAY * 60 * $days;

        $workload = DB::table('therapists')
            ->leftJoin('appointments', function ($join) use (
                $startDate,
                $endDate
            ) {
                $join->on(
                    'therapists.id',
                    '=',
                    'appointments.therapist_id'
                )
                    ->whereBetween(
                        'appointments.appointment_date',
                        [$startDate, $endDate]
                    )
                    ->where(
                        'appointments.status',
                        self::CONFIRMED_STATUS
                    );
            })
            ->select(
                'therapists.id',
                'therapists.name',
                DB::raw('COUNT(appointments.id) as appointments'),
                DB::raw('
                    COALESCE(
                        SUM(
                            COALESCE(
                                appointments.service_duration_minutes,
                                0
                            ) +
                            COALESCE(
                                appointments.addons_duration_minutes,
                                0
                            )
                        ),
                        0
                    ) as booked_minutes
                ')
            )
            ->groupBy('therapists.id', 'therapists.name')
            ->orderByDesc('appointments')
            ->get();

        return $workload->map(function ($row) use (
            $availableMinutesPerTherapist
        ) {
            $bookedMinutes = (int) $row->booked_minutes;

            $utilization = $availableMinutesPerTherapist > 0
                ? ($bookedMinutes / $availableMinutesPerTherapist) * 100
                : 0;

            return [
                'therapist' => $row->name,

                'appointments' => (int) $row->appointments,

                'booked_minutes' => $bookedMinutes,

                'booked_hours' => round($bookedMinutes / 60, 2),

                'available_hours' => round(
                    $availableMinutesPerTherapist / 60,
                    2
                ),

                'estimated_utilization_percentage' => round(
                    $utilization,
                    2
                ),
            ];
        })->values()->all();
    }

    /**
     * Customer analytics.
     *
     * A walk-in customer who later registers is treated as
     * the same customer through registered_user_id.
     */
    private function customerAnalytics(
        string $startDate,
        string $endDate
    ): array {
        $appointments = DB::table('appointments')
            ->leftJoin(
                'walk_in_customers',
                'appointments.walk_in_customer_id',
                '=',
                'walk_in_customers.id'
            )
            ->where('appointments.status', self::CONFIRMED_STATUS)
            ->whereDate('appointments.appointment_date', '<=', $endDate)
            ->select(
                'appointments.user_id',
                'appointments.walk_in_customer_id',
                'appointments.appointment_date',
                'walk_in_customers.registered_user_id'
            )
            ->get();

        $customers = [];

        foreach ($appointments as $appointment) {
            $registeredUserId = $appointment->user_id
                ?? $appointment->registered_user_id;

            if ($registeredUserId) {
                $key = 'user:' . $registeredUserId;
                $type = 'registered';
            } elseif ($appointment->walk_in_customer_id) {
                $key = 'walkin:' . $appointment->walk_in_customer_id;
                $type = 'walk_in';
            } else {
                // Do not merge appointments with unidentified customers.
                $key = 'appointment:' . spl_object_id($appointment);
                $type = 'unknown';
            }

            if (!isset($customers[$key])) {
                $customers[$key] = [
                    'type' => $type,
                    'total_appointments' => 0,
                    'period_appointments' => 0,
                    'first_appointment_date' => $appointment->appointment_date,
                ];
            }

            $customers[$key]['total_appointments']++;

            if ($appointment->appointment_date >= $startDate) {
                $customers[$key]['period_appointments']++;
            }

            if (
                $appointment->appointment_date <
                $customers[$key]['first_appointment_date']
            ) {
                $customers[$key]['first_appointment_date'] =
                    $appointment->appointment_date;
            }
        }

        $periodCustomers = array_filter(
            $customers,
            fn($customer) => $customer['period_appointments'] > 0
        );

        $registered = 0;
        $walkIns = 0;
        $newCustomers = 0;
        $returningCustomers = 0;

        foreach ($periodCustomers as $customer) {
            if ($customer['type'] === 'registered') {
                $registered++;
            } elseif ($customer['type'] === 'walk_in') {
                $walkIns++;
            }

            if ($customer['first_appointment_date'] >= $startDate) {
                $newCustomers++;
            } else {
                $returningCustomers++;
            }
        }

        return [
            'unique_customers' => count($periodCustomers),

            'registered_customers' => $registered,

            'walk_in_customers' => $walkIns,

            'new_customers' => $newCustomers,

            'returning_customers' => $returningCustomers,

            'repeat_customer_definition' =>
            'A returning customer has a confirmed appointment before the selected reporting period.',
        ];
    }

    /**
     * Appointment demand by date and weekday.
     */
    private function appointmentDemand(
        string $startDate,
        string $endDate
    ): array {
        $rows = DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->select(
                'appointment_date',
                DB::raw('COUNT(*) as appointments')
            )
            ->groupBy('appointment_date')
            ->orderByDesc('appointments')
            ->get();

        $weekdayCounts = [];

        foreach ($rows as $row) {
            $weekday = Carbon::parse(
                $row->appointment_date,
                self::TIMEZONE
            )->format('l');

            $weekdayCounts[$weekday] =
                ($weekdayCounts[$weekday] ?? 0) + (int) $row->appointments;
        }

        arsort($weekdayCounts);

        return [
            'busiest_dates' => $rows
                ->take(10)
                ->map(fn($row) => [
                    'date' => (string) $row->appointment_date,
                    'appointments' => (int) $row->appointments,
                ])
                ->values()
                ->all(),

            'appointments_by_weekday' => $weekdayCounts,
        ];
    }

    /**
     * Recorded revenue by appointment date.
     */
    private function dailyRevenue(
        string $startDate,
        string $endDate
    ): array {
        return DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', self::CONFIRMED_STATUS)
            ->select(
                'appointment_date',
                DB::raw('SUM(COALESCE(amount_paid, 0)) as revenue')
            )
            ->groupBy('appointment_date')
            ->orderBy('appointment_date')
            ->get()
            ->map(fn($row) => [
                'date' => (string) $row->appointment_date,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->values()
            ->all();
    }

    /**
     * Period comparison.
     */
    private function comparison(
        float|int $previous,
        float|int $current
    ): array {
        return [
            'previous' => $previous,
            'current' => $current,
            'difference' => round($current - $previous, 2),

            'percentage_change' => (float) $previous === 0.0
                ? null
                : round(
                    (($current - $previous) / $previous) * 100,
                    2
                ),
        ];
    }

    /**
     * Resolve requested reporting period.
     */
    private function resolvePeriod(string $question): array
    {
        $today = Carbon::now(self::TIMEZONE)->startOfDay();

        $text = mb_strtolower($question);

        if (preg_match(
            '/\b(?:from\s+)?(\d{4}-\d{2}-\d{2})\s+(?:to|through|until)\s+(\d{4}-\d{2}-\d{2})\b/',
            $text,
            $matches
        )) {
            try {
                $start = Carbon::createFromFormat(
                    '!Y-m-d',
                    $matches[1],
                    self::TIMEZONE
                );

                $end = Carbon::createFromFormat(
                    '!Y-m-d',
                    $matches[2],
                    self::TIMEZONE
                );

                if ($start && $end && $start->lte($end)) {
                    return [
                        $start->startOfDay(),
                        $end->endOfDay(),
                        $start->format('M j, Y') . ' to ' .
                            $end->format('M j, Y'),
                    ];
                }
            } catch (\Throwable $exception) {
                // Continue with normal period resolution.
            }
        }

        if (preg_match('/\byesterday\b/', $text)) {
            $start = $today->copy()->subDay();

            return [
                $start,
                $start->copy()->endOfDay(),
                'Yesterday',
            ];
        }

        if (preg_match('/\btoday\b/', $text)) {
            return [
                $today->copy(),
                $today->copy()->endOfDay(),
                'Today',
            ];
        }

        if (preg_match('/\blast week\b/', $text)) {
            $start = $today->copy()->subWeek()->startOfWeek();
            $end = $start->copy()->endOfWeek();

            return [$start, $end, 'Last week'];
        }

        if (preg_match('/\b(this week|weekly)\b/', $text)) {
            $start = $today->copy()->startOfWeek();

            return [
                $start,
                $today->copy()->endOfDay(),
                'This week to date',
            ];
        }

        if (preg_match('/\blast month\b/', $text)) {
            $start = $today->copy()->subMonthNoOverflow()->startOfMonth();

            return [
                $start,
                $start->copy()->endOfMonth(),
                'Last month',
            ];
        }

        if (preg_match('/\b(this month|month to date|monthly)\b/', $text)) {
            $start = $today->copy()->startOfMonth();

            return [
                $start,
                $today->copy()->endOfDay(),
                'This month to date',
            ];
        }

        if (preg_match('/\blast year\b/', $text)) {
            $start = $today->copy()->subYear()->startOfYear();

            return [
                $start,
                $start->copy()->endOfYear(),
                'Last year',
            ];
        }

        if (preg_match('/\b(this year|year to date|yearly|annually)\b/', $text)) {
            return [
                $today->copy()->startOfYear(),
                $today->copy()->endOfDay(),
                'This year to date',
            ];
        }

        $monthNames = [
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];

        foreach ($monthNames as $name => $month) {
            if (preg_match('/\b' . $name . '\b/', $text)) {
                preg_match('/\b(20\d{2})\b/', $text, $matches);

                $year = isset($matches[1])
                    ? (int) $matches[1]
                    : $today->year;

                if (
                    $year > $today->year ||
                    ($year === $today->year && $month > $today->month)
                ) {
                    return [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfDay(),
                        'This month to date',
                    ];
                }

                $start = Carbon::create(
                    $year,
                    $month,
                    1,
                    0,
                    0,
                    0,
                    self::TIMEZONE
                );

                $end = $start->copy()->endOfMonth();

                if ($year === $today->year && $month === $today->month) {
                    $end = $today->copy()->endOfDay();
                }

                return [
                    $start,
                    $end,
                    $start->format('F Y'),
                ];
            }
        }

        $start = $today->copy()->startOfMonth();

        return [
            $start,
            $today->copy()->endOfDay(),
            'This month to date',
        ];
    }
}
