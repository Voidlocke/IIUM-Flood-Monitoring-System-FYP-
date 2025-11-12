<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function create() {
        return view('report');
    }

    public function store(Request $request) {
        $request->validate([
            'location' => 'required',
            'description' => 'nullable',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'severity' => 'required',
        ]);

        UserReport::create($request->only(['location', 'description', 'latitude', 'longitude', 'severity']));

        return redirect('/')->with('success', 'Report submitted!');
    }
}
