<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FloodAlertMail;
use Illuminate\Support\Facades\Cache;

class SensorDataController extends Controller
{
    public function index()
    {
        $sensors = SensorData::where('is_active', 1)->get();

    }

    public function create()
    {
        return view('admin.sensors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        SensorData::create([
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'water_level' => 0,
            'alert_sent' => 0,
            'is_active' => 1,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Sensor added successfully.');
    }

    public function toggle($id)
    {
        $sensor = SensorData::findOrFail($id);
        $sensor->is_active = !$sensor->is_active;
        $sensor->save();

        return back()->with('success', 'Sensor status updated.');
    }

    public function checkAlerts()
    {
        $DANGER_LEVEL = 30; // cm
        $SAFE_LEVEL   = 20; // cm

        $sensors = SensorData::where('is_active', 1)->get();

        //  GLOBAL STATE: has any alert already been sent?
        $globalAlertSent = SensorData::where('alert_sent', 1)->exists();

        //  CHECK IF ANY SENSOR IS DANGEROUS
        $dangerSensors = $sensors->filter(fn ($s) => $s->water_level >= $DANGER_LEVEL);

        //  SEND ONE EMAIL ONLY
        if ($dangerSensors->count() > 0 && !$globalAlertSent) {

            $message = "⚠️ FLOOD ALERT\n\n";

            foreach ($dangerSensors as $sensor) {
                $message .=
                    "Location: {$sensor->location}\n"
                . "Water Level: {$sensor->water_level} cm\n\n";
            }

            $users = User::where('receive_flood_alerts', true)->get();

            foreach ($users as $user) {
                Mail::to($user->email)->send(new FloodAlertMail($message));
            }

            // LOCK THE SYSTEM (PREVENT SPAM)
            SensorData::query()->update(['alert_sent' => 1]);

            return response()->json(['status' => 'alert_sent']);
        }

        // RESET ONLY WHEN ALL SENSORS ARE SAFE
        $allSafe = $sensors->every(fn ($s) => $s->water_level < $SAFE_LEVEL);

        if ($allSafe && $globalAlertSent) {
            SensorData::query()->update(['alert_sent' => 0]);
            return response()->json(['status' => 'alert_reset']);
        }

        return response()->json(['status' => 'no_change']);
}


}
