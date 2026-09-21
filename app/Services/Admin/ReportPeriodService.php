<?php

namespace App\Services\Admin;

use App\Enums\Admin\Report\ReportPeriod;
use Carbon\Carbon;

class ReportPeriodService
{
    public function getRange(ReportPeriod $period): array
    {
        return match ($period) {
            ReportPeriod::TODAY => [Carbon::today('Asia/Manila')->startOfDay(), Carbon::today('Asia/Manila')->endOfDay()],

            ReportPeriod::YESTERDAY => [Carbon::yesterday('Asia/Manila')->startOfDay(), Carbon::yesterday('Asia/Manila')->endOfDay()],

            ReportPeriod::THIS_WEEK => [Carbon::now('Asia/Manila')->startOfWeek(), Carbon::now('Asia/Manila')->endOfWeek()],

            ReportPeriod::THIS_MONTH => [Carbon::now('Asia/Manila')->startOfMonth(), Carbon::now('Asia/Manila')->endOfMonth()],

            ReportPeriod::THIS_QUARTER => [Carbon::now('Asia/Manila')->startOfQuarter(), Carbon::now('Asia/Manila')->endOfQuarter()],

            ReportPeriod::THIS_YEAR => [Carbon::now('Asia/Manila')->startOfYear(), Carbon::now('Asia/Manila')->endOfYear()],
        };
    }

    public function getDays(ReportPeriod $period, Carbon $start, Carbon $end): array
    {
        if ($period === ReportPeriod::TODAY || $period === ReportPeriod::YESTERDAY) {
            return [$start->copy()];
        }

        return collect(\Carbon\CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()))->all();
    }
}
