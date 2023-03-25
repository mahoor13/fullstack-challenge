<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'weather_service',
        'latitude',
        'longitude',
        'weather_info',
    ];

    protected $casts = [
        'weather_info' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}