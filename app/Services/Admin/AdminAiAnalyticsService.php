<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAiAnalyticsService
{
    private const TIMEZONE = 'Asia/Manila';

    private const CONFIRMED_STATUS = 'confirm';

    private const EXCLUDED_STATUSES = ['cancelled', 'rejected', 'failed', 'no show'];

    /**
     * Build analytics for the question being asked.
     */
    public function contextForQuestion(string $question): array
    {
        [$start, $end, $periodLabel] = $this->resolvePeriod($question);

        // Compare with the immediately preceding period of equal length.
        $numberOfDays = $start->diffInDays($end) + 1;

        $previousEnd = $start->copy()->subDay()->endOfDay();

        $previousStart = $previousEnd
            ->copy()
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
            ],

            'comparison_period' => [
                'start' => $previousStart->toDateString(),
                'end' => $previousEnd->toDateString(),
            ],

            'current' => $current,
            'previous' => $previous,

            'revenue_change' => $this->percentageChange($previous['recorded_revenue'], $current['recorded_revenue']),

            'appointment_change' => $this->percentageChange($previous['appointment_count'], $current['appointment_count']),

            'all_time_reference' => [
                'services_count' => DB::table('services')->count(),
                'therapists_count' => DB::table('therapists')->count(),
            ],

            'definitions' => [
                'recorded_revenue' => 'Sum of amount_paid for confirmed appointments scheduled in the selected date range. Revenue is grouped by appointment date, not payment date.',

                'appointment_count' => 'Number of confirmed appointments scheduled in the selected date range.',

                'status_breakdown' => 'Counts of appointments by status in the selected date range.',

                'service_popularity' => 'Confirmed appointment count per service in the selected date range.',

                'therapist_workload' => 'Confirmed appointment count per therapist in the selected date range.',

                'comparison' => 'Compared with the immediately preceding period containing the same number of calendar days.',
            ],
        ];
    }

    private function periodMetrics(Carbon $start, Carbon $end): array
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $base = DB::table('appointments')->whereBetween('appointment_date', [$startDate, $endDate]);

        $confirmed = (clone $base)->where('status', self::CONFIRMED_STATUS);

        $revenue = (clone $confirmed)->sum(DB::raw('COALESCE(amount_paid, 0)'));

        $appointmentCount = (clone $confirmed)->count();

        $statusRows = (clone $base)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->get();

        $statuses = [];

        foreach ($statusRows as $row) {
            $statuses[$row->status] = (int) $row->total;
        }

        $servicePopularity = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointments.appointment_date', [$startDate, $endDate])
            ->where('appointments.status', self::CONFIRMED_STATUS)
            ->select('services.id', 'services.name', DB::raw('COUNT(appointments.id) as appointments'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('appointments')
            ->limit(10)
            ->get()
            ->map(
                fn($row) => [
                    'service' => $row->name,
                    'appointments' => (int) $row->appointments,
                ],
            )
            ->values()
            ->all();

        $therapistWorkload = DB::table('appointments')
            ->join('therapists', 'appointments.therapist_id', '=', 'therapists.id')
            ->whereBetween('appointments.appointment_date', [$startDate, $endDate])
            ->where('appointments.status', self::CONFIRMED_STATUS)
            ->select('therapists.id', 'therapists.name', DB::raw('COUNT(appointments.id) as appointments'))
            ->groupBy('therapists.id', 'therapists.name')
            ->orderByDesc('appointments')
            ->get()
            ->map(
                fn($row) => [
                    'therapist' => $row->name,
                    'appointments' => (int) $row->appointments,
                ],
            )
            ->values()
            ->all();

        $dailyRevenue = DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->where('status', self::CONFIRMED_STATUS)
            ->select('appointment_date', DB::raw('SUM(COALESCE(amount_paid, 0)) as revenue'))
            ->groupBy('appointment_date')
            ->orderBy('appointment_date')
            ->get()
            ->map(
                fn($row) => [
                    'date' => (string) $row->appointment_date,
                    'revenue' => (float) $row->revenue,
                ],
            )
            ->values()
            ->all();

        return [
            'recorded_revenue' => round((float) $revenue, 2),
            'appointment_count' => (int) $appointmentCount,
            'status_breakdown' => $statuses,
            'service_popularity' => $servicePopularity,
            'therapist_workload' => $therapistWorkload,
            'daily_revenue' => $dailyRevenue,
        ];
    }

    private function resolvePeriod(string $question): array
    {
        $today = Carbon::now(self::TIMEZONE)->startOfDay();
        $text = mb_strtolower($question);

        // Explicit date range, for example:
        // "October 1 to October 15, 2026"
        if (preg_match('/\b(?:from\s+)?(\d{4}-\d{2}-\d{2})\s+(?:to|through|until)\s+(\d{4}-\d{2}-\d{2})\b/', $text, $matches)) {
            $start = Carbon::createFromFormat('!Y-m-d', $matches[1], self::TIMEZONE);

            $end = Carbon::createFromFormat('!Y-m-d', $matches[2], self::TIMEZONE);

            if ($start && $end && $start->lte($end)) {
                return [$start->startOfDay(), $end->endOfDay(), $start->format('M j, Y') . ' to ' . $end->format('M j, Y')];
            }
        }

        if (preg_match('/\byesterday\b/', $text)) {
            $start = $today->copy()->subDay();

            return [$start, $start->copy()->endOfDay(), 'Yesterday'];
        }

        if (preg_match('/\btoday\b/', $text)) {
            return [$today->copy(), $today->copy()->endOfDay(), 'Today'];
        }

        if (preg_match('/\blast week\b/', $text)) {
            $start = $today->copy()->subWeek()->startOfWeek();
            $end = $start->copy()->endOfWeek();

            return [$start, $end, 'Last week'];
        }

        if (preg_match('/\b(this week|weekly)\b/', $text)) {
            $start = $today->copy()->startOfWeek();

            return [$start, $today->copy()->endOfDay(), 'This week to date'];
        }

        if (preg_match('/\blast month\b/', $text)) {
            $start = $today->copy()->subMonthNoOverflow()->startOfMonth();

            return [$start, $start->copy()->endOfMonth(), 'Last month'];
        }

        if (preg_match('/\b(this month|month to date|monthly)\b/', $text)) {
            $start = $today->copy()->startOfMonth();

            return [$start, $start->copy()->endOfMonth(), 'This month'];
        }

        if (preg_match('/\blast year\b/', $text)) {
            $start = $today->copy()->subYear()->startOfYear();

            return [$start, $start->copy()->endOfYear(), 'Last year'];
        }

        if (preg_match('/\b(this year|year to date|yearly|annually)\b/', $text)) {
            return [$today->copy()->startOfYear(), $today->copy()->endOfDay(), 'This year to date'];
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

                $year = isset($matches[1]) ? (int) $matches[1] : $today->year;

                // Avoid treating a future month as actual revenue.
                if ($year > $today->year || ($year === $today->year && $month > $today->month)) {
                    return [$today->copy()->startOfMonth(), $today->copy()->endOfDay(), 'This month to date'];
                }

                $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE);

                $end = $start->copy()->endOfMonth();

                if ($year === $today->year && $month === $today->month) {
                    $end = $today->copy()->endOfDay();
                }

                return [$start, $end, $start->format('F Y')];
            }
        }

        $start = $today->copy()->startOfMonth();

        return [$start, $start->copy()->endOfMonth(), 'This month'];
    }

    private function percentageChange(float|int $previous, float|int $current): ?float
    {
        if ((float) $previous === 0.0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
