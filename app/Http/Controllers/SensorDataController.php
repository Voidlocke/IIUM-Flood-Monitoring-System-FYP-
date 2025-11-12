<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function index()
    {
        return SensorData::latest()->take(10)->get(); // return latest 10 dummy records
    }
}
