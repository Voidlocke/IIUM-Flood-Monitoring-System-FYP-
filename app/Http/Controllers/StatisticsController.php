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

        // 1. Get latest sensor data for display
        $latestSensorData = SensorData::orderBy('created_at', 'desc')->get();

        // 2. Compute water level ranges
        $sensorSeverityCounts = [
            'low' => SensorData::where('water_level', '<=', 0.20)->count(),
            'moderate' => SensorData::whereBetween('water_level', [0.21, 0.50])->count(),
            'high' => SensorData::whereBetween('water_level', [0.51, 1.00])->count(),
            'severe' => SensorData::where('water_level', '>', 1.00)->count(),
        ];

        // 3. Sensor water-level time series (based on the filter range)
        $sensorData = SensorData::selectRaw('DATE(created_at) as date, AVG(water_level) as avg_level')
            ->when($range == '7d', fn($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->when($range == '1m', fn($q) => $q->where('created_at', '>=', now()->subMonth()))
            ->when($range == '6m', fn($q) => $q->where('created_at', '>=', now()->subMonths(6)))
            ->when($range == '1y', fn($q) => $q->where('created_at', '>=', now()->subYear()))
            ->when($range == '5y', fn($q) => $q->where('created_at', '>=', now()->subYears(5)))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('statistics', [
            'totalReports'      => $totalReports,
            'severityCounts'    => $severityCounts,
            'reports'           => $reports,
            'sensorCount'       => $sensorCount,
            'highWaterSensors'  => $highWaterSensors,
            'range'             => $range,

            // NEW SENSOR DATA
            'sensorSeverityCounts' => $sensorSeverityCounts,
            'sensorData' => $sensorData,
            'latestSensorData' => $latestSensorData,
        ]);
    }
}

