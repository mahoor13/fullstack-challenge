<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;

class WeatherScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'a schedule to run the weather fetch command every X seconds';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $rate = env('WEATHER_API_CALL_RATE', 10);
        // add 1 to rate to stop api calls before one minute
        $timeout = 60 / ($rate + 1);
        $output = null;
        for ($cnt=0; $cnt < $rate; $cnt++) {
            Artisan::call('weather:fetch', [], $output);
            echo $output;
            usleep(1000000 * $timeout);
        } 
    }
}
