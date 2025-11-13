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

public function approve($id) {
    UserReport::where('id', $id)->update(['status' => 'approved']);
    return back()->with('success', 'Report approved.');
}

public function clear($id) {
    UserReport::where('id', $id)->update(['status' => 'cleared']);
    return back()->with('success', 'Report marked as cleared.');
}

public function destroy($id) {
    UserReport::findOrFail($id)->delete();
    return back()->with('success', 'Report deleted.');
}
}
