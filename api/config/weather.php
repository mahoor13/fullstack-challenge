<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Weather
    |--------------------------------------------------------------------------
    |
    | It is set base on Valid API that give request 
    |
    */

    'primary' => 'open_weather',
    'secondary' => 'weather_gov',

    // 'primary' => 'weather_gov',
    // 'secondary' => 'open_weather',
    
    /*
    |--------------------------------------------------------------------------
    | Weather Configs
    |--------------------------------------------------------------------------
    |
    | Here we may configure as many weather config as we wish
    |
    */

    'open_weather' => [
        'service' => 'OpenWeatherService',
        'url' => 'https://api.openweathermap.org/data/2.5/weather?lat=%f&lon=%f&lang=%s&units=%s&appid=%s',
        'api_key' => env('OPENWAETHER_API_KEY', ''),
        'lang' => env('OPENWAETHER_API_LANG', 'en'),
        'unit' => env('OPENWAETHER_UNIT', 'metric'), // metric / imperial
    ],

    'weather_gov' => [
        'service' => 'WeatherGovService',
        'url' => 'https://api.weather.gov/points/%f,%f',
        'lang' => env('WEATHER_GOV_API_LANG', 'en'),
        'unit' => env('WEATHER_GOV_UNIT', 'si') // si / us
    ],

];
