<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{

    public function __construct(private DashboardService $dashboardService)
    {

    }
    public function index(Request $request)
    {
        // 年月パラメータを取得
        $year = request('year', Carbon::now()->year);
        $month = request('month', Carbon::now()->month);

        $date = $this->dashboardService->getSummary($year, $month);

        return view('dashboard', $date);
    }
}
