<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffDashboardService;

class StaffDashboardController extends Controller
{
    public function __construct(protected StaffDashboardService $dashboardService) {}

    public function dashboard()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('staff.dashboard', $data);
    }
}
