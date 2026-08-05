<?php

namespace App\Http\Controllers;

use App\Models\TruckLoad;
use App\Models\Supplier;
use Illuminate\Support\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalVolumeMonth = TruckLoad::whereYear('date_scaled', $currentYear)
            ->whereMonth('date_scaled', $currentMonth)
            ->sum('total_volume');

        $activeTrucksToday = TruckLoad::whereDate('date_scaled', Carbon::today())->count();
        $totalSuppliers = Supplier::count();
        $totalLogsAll = TruckLoad::sum('total_logs');

        return view('landing', compact(
            'totalVolumeMonth',
            'activeTrucksToday',
            'totalSuppliers',
            'totalLogsAll'
        ));
    }
}
