<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'description',
        'latitude',
        'longitude',
        'severity',
        'image',
        'status',
        'user_id',
    ];
}
