<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function reports(Request $request)
    {
        $periodOptions = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
        ];

        $period = $request->input('period', 'today');
        if (! is_string($period) || ! array_key_exists($period, $periodOptions)) {
            $period = 'today';
        }

        $periodLabel = $periodOptions[$period];
        [$start, $end] = $this->getPeriodRange($period);

        $baseQuery = DB::table('appointments')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('appointments.status', '!=', 'cancelled');

        $totalRevenue = (clone $baseQuery)->sum(DB::raw('COALESCE(amount_paid, 0)'));
        $totalAppointments = (clone $baseQuery)->count();

        $peakHours = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(appointment_time, '%H:00') as hour, COUNT(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

            $serviceDistribution = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointments.appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('appointments.status', '!=', 'cancelled')
            ->selectRaw('services.id, services.name as service_name, services.description, services.duration_minutes, COUNT(*) as total')
            ->groupBy('services.id', 'services.name', 'services.description','services.duration_minutes')
            ->orderByDesc('total')
            ->get();

        $revenueTrend = $this->buildRevenueTrend($period, $start, $end);

        return view('admin.reports', compact(
            'period',
            'periodLabel',
            'periodOptions',
            'totalRevenue',
            'totalAppointments',
            'peakHours',
            'serviceDistribution',
            'revenueTrend'
        ));
    }

    private function getPeriodRange(string $period): array
    {
        return match ($period) {
            'yesterday' => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'this_quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
        };
    }

    private function buildRevenueTrend(string $period, Carbon $start, Carbon $end): array
    {
        $labels = [];
        $values = [];

        if ($period === 'today' || $period === 'yesterday') {
            $days = collect([$start->copy()]);
        } else {
            $days = collect(CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()));
        }

        $dailyRevenue = DB::table('appointments')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('appointments.status', '!=', 'cancelled')
            ->selectRaw('appointment_date, SUM(COALESCE(amount_paid, 0)) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');

        foreach ($days as $day) {
            $date = $day->format('Y-m-d');
            $labels[] = $day->format('M d');
            $values[] = (float) ($dailyRevenue[$date] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}