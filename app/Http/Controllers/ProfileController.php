<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Reports by this user
        $reports = UserReport::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Severity counts for this user only
        $severityCounts = UserReport::where('user_id', $user->id)
            ->selectRaw('severity, COUNT(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        // Reports over time (grouped by date)
        $reportsOverTime = UserReport::where('user_id', $user->id)
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
