<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // ALL reports for table display
        $reports = UserReport::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'cleared'])
            ->orderBy('created_at', 'desc')
            ->get();

        // VERIFIED reports only (approved + cleared)
        $verifiedReports = UserReport::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'cleared']);

        // Severity counts (verified only)
        $severityCounts = (clone $verifiedReports)
            ->selectRaw('severity, COUNT(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        // Reports over time (verified only)
        $reportsOverTime = (clone $verifiedReports)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('profile.show', compact(
            'user',
            'reports',
            'severityCounts',
            'reportsOverTime'
        ));
    }

    public function updateEmailPreferences(Request $request)
    {
        $request->validate([
            'receive_flood_alerts' => 'nullable|boolean',
        ]);

        $user = $request->user();

        $user->update([
            'receive_flood_alerts' => $request->has('receive_flood_alerts'),
        ]);

        return back()->with('success', 'Email preferences updated.');
    }

}
