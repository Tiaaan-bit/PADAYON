<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffDashboardService;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function __construct(protected StaffDashboardService $dashboardService) {}

    public function dashboard(): View
    {
        return view('staff.dashboard', $this->dashboardService->getDashboardData());
    }
}
