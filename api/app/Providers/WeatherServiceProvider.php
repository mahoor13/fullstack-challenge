<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use App\Services\WeatherService;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // register Weather Service to proxy and translate each weather service to a standard format
        $this->app->singleton(WeatherService::class, function ($app) {
            // find primary service config name
            $primaryConfig = Cache::get('current_weather_service') ?? config('weather.primary');
            // return primary config value
            $config = config('weather.' . $primaryConfig);
            // return service instance
            return new WeatherService($config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/weather.php' => config_path('weather.php'),
        ], 'config');
    }
}
