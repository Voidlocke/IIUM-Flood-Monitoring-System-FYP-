<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;
use App\Models\SensorData;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Get the filter from the URL (?filter=pending)
        $filter = $request->query('filter', 'all');

        // Base query
        $query = UserReport::orderBy('created_at', 'desc');

        // Apply filtering
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        }
        elseif ($filter === 'approved') {
            $query->where('status', 'approved');
        }
        elseif ($filter === 'cleared') {
            $query->where('status', 'cleared');
        }

        // Fetch filtered reports
        $reports = $query->get();

        // Fetch sensor data
        $sensors = SensorData::all();

        return view('admin.dashboard', compact('sensors', 'reports', 'filter'));
    }

    public function approve($id)
    {
        UserReport::where('id', $id)->update(['status' => 'approved']);
        return back()->with('success', 'Report approved.');
    }

    public function clear($id)
    {
        UserReport::where('id', $id)->update(['status' => 'cleared']);
        return back()->with('success', 'Report marked as cleared.');
    }

    public function destroy($id)
    {
        UserReport::findOrFail($id)->delete();
        return back()->with('success', 'Report deleted.');
    }
}
