<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\StatistiqueService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $site = Site::latest()->first();
        $service = new StatistiqueService($site);
        $resume = $service->resume(
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        );

        return view('dashboard', compact('resume'));
    }
}
