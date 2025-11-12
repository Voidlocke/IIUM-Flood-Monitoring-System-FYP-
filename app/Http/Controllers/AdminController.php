<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;
use App\Models\SensorData;

class AdminController extends Controller
{
    public function index() {
    $sensors = SensorData::all();
    $reports = UserReport::all();
    return view('admin.dashboard', compact('sensors', 'reports'));
}

public function verify($id) {
    $report = UserReport::findOrFail($id);
    $report->verified = true;
    $report->save();
    return back()->with('success', 'Report verified.');
}

public function clear($id) {
    $report = UserReport::findOrFail($id);
    $report->active = false;
    $report->save();
    return back()->with('success', 'Report marked as cleared.');
}

public function destroy($id) {
    UserReport::findOrFail($id)->delete();
    return back()->with('success', 'Report deleted.');
}
}
