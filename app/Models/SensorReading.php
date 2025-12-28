<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = ['sensor_data_id', 'water_level'];

    public function sensor()
    {
        return $this->belongsTo(SensorData::class);
    }
}
