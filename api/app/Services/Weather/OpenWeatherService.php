<?php

namespace App\Services\Weather;

use Http;
use Throwable;
use Exception;
use App\Traits\UnitConversionTrait;


/**
 * This class fetch weather forecast information from openweathermap.org API
 * documentation: https://openweathermap.org/current
 * 
 */
class OpenWeatherService
{
    use UnitConversionTrait;

    /**
     * constructor to get config (needs php 8.0+)
     *
     * @param array $config
     */
    public function __construct(protected array $config)
    {
    }
  /**
     * fetch and return current forecast information
     *
     * @param float $lat
     * @param float $lng
     * @return array|Throwable
     */
    public function currentForecast(float $lat, float $lng): array|Throwable
  
    {
        $forecastURL = sprintf(
            $this->config['url'],
            $lat,
            $lng,
            $this->config['lang'],
            $this->config['unit'],
            $this->config['api_key'],
        );

        if ($forecastURL !== null) {
            $forecastResponse = Http::asJson()
                ->get($forecastURL);
            
            if ($forecastResponse->successful()) {
                $data = $forecastResponse->json();
            }
        }
        if (empty($data)) {
            throw new Exception('Can not fetch forecast data', -1);
        }

        return $this->normalizeForecastData($data);
    }

    /**
     * normalize raw json data into a standard format
     *
     * @param array $rawData
     * @return array|Throwable
     */
    public function normalizeForecastData(array $rawData): array|Throwable
    {
        // I didn't apply null safe to the raw data indexes to return exception if error accured.
        $data = [
            'temperature' => (float)$rawData['main']['temp'],
            'temperatureUnit' => $this->config['unit'] == 'metric' ? 'C' : 'F',
            'windSpeed' => (float)$rawData['wind']['speed'],
            'windDirection' => $this->windDirection($rawData['wind']['deg']),
            'forecast' => $rawData['weather'][0]['main'],
        ];

        return $data;
    }
}