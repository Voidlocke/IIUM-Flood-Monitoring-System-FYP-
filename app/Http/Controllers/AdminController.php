<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;
use App\Models\SensorData;
use Illuminate\Support\Facades\Mail;
use App\Mail\FloodAlertMail;


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
        $report = UserReport::findOrFail($id);

        $report->status = 'approved';
        $report->save();

        // Build alert message
        $message = "⚠️ New Flood Report Approved\n\n" .
                    "Location: {$report->location}\n" .
                    "Severity: {$report->severity}\n" .
                    "Description: {$report->description}";

        // Send flood alert email to all users
        foreach (\App\Models\User::all() as $user) {
            Mail::to($user->email)->send(new FloodAlertMail($message));
        }

        return back()->with('success', 'Report approved and alert sent!');
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
