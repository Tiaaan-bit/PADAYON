<?php

namespace App\Repositories\Admin\Report;

use App\Repositories\Admin\Report\ReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;


class ReportRepository implements ReportRepositoryInterface
{
    private function baseQuery(Carbon $start, Carbon $end): Builder
    {
        return DB::table('appointments')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('appointments.status', 'confirm');
    }

    public function getTotalRevenue(Carbon $start, Carbon $end): float
    {
        return (float) $this->baseQuery($start, $end)->sum(DB::raw('COALESCE(amount_paid, 0)'));
    }

    public function getTotalAppointments(Carbon $start, Carbon $end): int
    {
        return $this->baseQuery($start, $end)->count();
    }

    public function getPeakHours(Carbon $start, Carbon $end): Collection
    {
        return $this->baseQuery($start, $end)
            ->selectRaw(
                "
                DATE_FORMAT(appointment_time, '%H:00') as hour,
                COUNT(*) as total
            ",
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    public function getServiceDistribution(Carbon $start, Carbon $end): Collection
    {
        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointments.appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('appointments.status', 'confirm')
            ->selectRaw(
                "
                services.id,
                services.name as service_name,
                services.description,
                services.duration_minutes,
                COUNT(*) as total
            ",
            )
            ->groupBy('services.id', 'services.name', 'services.description', 'services.duration_minutes')
            ->orderByDesc('total')
            ->get();
    }

    public function getDailyRevenue(Carbon $start, Carbon $end): Collection
    {
        return $this->baseQuery($start, $end)
            ->selectRaw(
                "
                appointment_date,
                SUM(COALESCE(amount_paid, 0)) as total
            ",
            )
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date');
    }
}
