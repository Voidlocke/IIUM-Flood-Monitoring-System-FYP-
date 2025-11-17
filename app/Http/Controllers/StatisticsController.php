<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', '7d');

        $dateFrom = match ($range) {
            '7d' => now()->subDays(7),
            '1m' => now()->subMonth(),
            '6m' => now()->subMonths(6),
            '1y' => now()->subYear(),
            '5y' => now()->subYears(5),
            default => now()->subDays(7),
        };

        // Base query: only approved + cleared reports
        $baseReports = UserReport::whereIn('status', ['approved', 'cleared']);

        // Total reports (approved + cleared)
        $totalReports = (clone $baseReports)->count();

        // Severity counts (approved + cleared)
        $severityCounts = (clone $baseReports)
            ->select('severity', DB::raw('count(*) as total'))
            ->groupBy('severity')
            ->pluck('total', 'severity');

        // Reports over time (for selected range)
        $reports = (clone $baseReports)
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sensor summary
        $sensorCount = SensorData::count();
        $highWaterSensors = SensorData::where('water_level', '>', 20)->count();

        return view('statistics', [
            'totalReports'      => $totalReports,
            'severityCounts'    => $severityCounts,
            'reports'           => $reports,
            'sensorCount'       => $sensorCount,
            'highWaterSensors'  => $highWaterSensors,
            'range'             => $range,
        ]);
    }
}

