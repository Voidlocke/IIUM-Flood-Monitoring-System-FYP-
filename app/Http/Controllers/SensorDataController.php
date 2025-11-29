<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FloodAlertMail;

class SensorDataController extends Controller
{
    public function index()
    {
        $sensors = SensorData::all();

        foreach ($sensors as $sensor) {

            $waterLevel = floatval($sensor->water_level) / 100; // converts cm → meters

            // If water level passes 0.20m and alert not sent yet
            if ($waterLevel > 0.20 && !$sensor->alert_sent) {

                $message = "⚠️ Sensor Flood Warning\n\n" .
                           "Sensor Location: {$sensor->location}\n" .
                           "Water Level: {$waterLevel}m\n" .
                           "Threshold: 0.20m";

                // Email all users
                foreach (\App\Models\User::all() as $user) {
                    Mail::to($user->email)->send(new FloodAlertMail($message));
                }

                // Prevent spam — mark alert sent
                $sensor->alert_sent = true;
                $sensor->save();
            }

            // Reset alert when level drops (optional)
            if ($sensor->alert_sent && $waterLevel <= 0.20) {
                $sensor->alert_sent = false;
                $sensor->save();
            }
        }

        return SensorData::latest()->take(10)->get();
    }
}
