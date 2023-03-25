<?php

namespace App\Services;

class WeatherService
{

    public function __construct(protected array $config)
    {
    }

  /**
     * fetch and return current forecast information
     *
     * @param float $lat
     * @param float $lng
     * @return array
     */
    public function currentForecast(float $lat, float $lng): array
    {
        $class = '\\App\\Services\\Weather\\'.$this->config['service'];
        $instance = new $class($this->config);

        return $instance->currentForecast($lat, $lng);
    }
}
