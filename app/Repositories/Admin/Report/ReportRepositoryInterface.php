<?php

namespace App\Repositories\Admin\Report;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface ReportRepositoryInterface
{
    public function getTotalRevenue(Carbon $start, Carbon $end): float;

    public function getTotalAppointments(Carbon $start, Carbon $end): int;

    public function getPeakHours(Carbon $start, Carbon $end): Collection;

    public function getServiceDistribution(Carbon $start, Carbon $end): Collection;

    public function getDailyRevenue(Carbon $start, Carbon $end): Collection;
}
