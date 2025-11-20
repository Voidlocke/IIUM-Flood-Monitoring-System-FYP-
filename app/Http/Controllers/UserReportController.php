<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;


class UserReportController extends Controller
{
    public function create() {
        return view('report');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'description' => 'nullable',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'severity' => 'required|in:ankle,knee,waist,chest,head',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        // Add the user_id
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Read image using v3 syntax
            $img = Image::read($image);

            // Resize with max dimension 1280px (auto aspect ratio)
            $img->scaleDown(1280);

            // Create filename
            $filename = time() . '_' . $image->getClientOriginalName();

            // Save the resized image
            $img->save(storage_path('app/public/reports/' . $filename));

            // Save path in database
            $validated['image'] = 'reports/' . $filename;
        }

        $validated['status'] = 'pending';
        UserReport::create($validated);

        return redirect('/')->with('success', 'Report submitted! Waiting for admin approval');
    }
}
