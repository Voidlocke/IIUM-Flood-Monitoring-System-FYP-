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
        $users = UserReport::where('status', 'approved')->get();

        return response()->json([
            'sensors' => $sensors,
            'user_reports' => UserReport::where('status', 'approved')->get(),
        ]);
    }
}


