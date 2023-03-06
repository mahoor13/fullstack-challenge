<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WeatherHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WeatherFetchCommand extends Command
{

    private $weatherProviderCacheTTL = 3600;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch weather data for one user and cache it';

    /**
     * Execute the console command.
     */
    public function handle(WeatherService $weatherService): void
    {
        $user = User::orderBy('last_weather_update', 'ASC')
            ->first();

        try {
            $weather_info = $weatherService->currentForecast($user->latitude, $user->longitude);
            // store weather history
            $weatherHistory = new WeatherHistory;
            $weatherHistory->weather_service = Cache::get('current_weather_service') ?? config('weather.primary');
            $weatherHistory->latitude = $user->latitude;
            $weatherHistory->longitude = $user->longitude;
            $weatherHistory->weather_info = $weather_info;
            // set user_id
            $weatherHistory->user()->associate($user);
            // save to db
            $weatherHistory->save();

            // update user last_weather_update timestamp
            $user->last_weather_update = Carbon::now()->toDateTimeString();
            $user->save();

        } catch (Throwable $e) {
            
            // switch the current weather service provider for the next hour if service fails
            if (Cache::get('current_weather_service') == config('weather.secondary')) {
                Cache::set('current_weather_service', config('weather.primary'), $this->weatherProviderCacheTTL);
            } else {
                Cache::set('current_weather_service', config('weather.secondary'), $this->weatherProviderCacheTTL);
            }
            $this->warn('current_weather_service switched to '.Cache::get('current_weather_service'));
            return;
        }
        
        // store user weather info
        dump($user->name, $weather_info);
    }
}
