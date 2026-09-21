<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportFilterRequest;
use App\Repositories\Admin\Report\ReportRepositoryInterface;
use App\Services\Admin\ReportPeriodService;
use Illuminate\View\View;

class AdminReportsController extends Controller
{
    public function reports(ReportFilterRequest $request, ReportRepositoryInterface $reports, ReportPeriodService $periodService): View
    {
        $period = $request->period();

        [$start, $end] = $periodService->getRange($period);

        $totalRevenue = $reports->getTotalRevenue($start, $end);

        $totalAppointments = $reports->getTotalAppointments($start, $end);

        $peakHours = $reports->getPeakHours($start, $end);

        $serviceDistribution = $reports->getServiceDistribution($start, $end);

        $dailyRevenue = $reports->getDailyRevenue($start, $end);

        $days = $periodService->getDays($period, $start, $end);

        $revenueTrend = [
            'labels' => [],
            'values' => [],
        ];

        foreach ($days as $day) {
            $date = $day->format('Y-m-d');

            $revenueTrend['labels'][] = $day->format('M d');

            $revenueTrend['values'][] = (float) ($dailyRevenue[$date] ?? 0);
        }

        return view('admin.reports', [
            'period' => $period->value,
            'periodLabel' => $period->label(),
            'periodOptions' => collect(\App\Enums\Admin\Report\ReportPeriod::cases())
                ->mapWithKeys(
                    fn($period) => [
                        $period->value => $period->label(),
                    ],
                )
                ->all(),

            'totalRevenue' => $totalRevenue,
            'totalAppointments' => $totalAppointments,
            'peakHours' => $peakHours,
            'serviceDistribution' => $serviceDistribution,
            'revenueTrend' => $revenueTrend,
        ]);
    }
}
