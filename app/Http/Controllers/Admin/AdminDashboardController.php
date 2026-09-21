<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;

class AdminDashboardController extends Controller
{
    public function __construct(protected AdminDashboardService $dashboardService) {}

    public function dashboard()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('admin.dashboard', $data);
    }
}
