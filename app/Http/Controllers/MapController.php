<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\UserReport;

class MapController extends Controller
{
    public function index() {
        return view('welcome'); // your map view
    }

    public function floodData() {
        $sensors = SensorData::all();
        $users = UserReport::all();

        return response()->json([
            'sensors' => $sensors,
            'user_reports' => UserReport::where('created_at', '>=', now()->subDays(7))->get(),
        ]);
    }
}


