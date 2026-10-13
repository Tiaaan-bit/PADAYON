<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(protected AdminDashboardService $dashboardService) {}

    public function dashboard(): View
    {
        return view('admin.dashboard', $this->dashboardService->getDashboardData());
    }
}
