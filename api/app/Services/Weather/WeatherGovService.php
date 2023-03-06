<?php

namespace App\Services\Weather;

use Http;
use Throwable;
use Exception;
use Illuminate\Support\Facades\Cache;
use App\Traits\UnitConversionTrait;

/**
 * This class fetch weather forecast information from weather.gov API
 * documentation: https://www.weather.gov/documentation/services-web-api
 * 
 */
class WeatherGovService
{
    use UnitConversionTrait;

    private $urlCacheTTL = 36000;

    /**
     * fetch and return current forecast information
     *
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
    public function currentForecast(float $lat, float $lng): ?array
    {
        $forecastURL = $this->getForecastURL($lat, $lng);

        if ($forecastURL !== null) {
            $forecastResponse = Http::asJson()
                ->get($forecastURL);
            
        // dd($forecastURL , $forecastResponse->json());
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
     * fetch forcast URL from the selected latitude and longitude
     *
     * @param float $lat
     * @param float $lng
     * @return string|Throwable
     */
    protected function getForecastURL(float $lat, float $lng): string|Throwable
    {
        // try to read forecast URL from cache 
        $cacheKey = __CLASS__ . "_forecast_url_{$lat}_{$lng}";
        $cachedURL = Cache::get($cacheKey);
        if ($cachedURL) {
            return (string)$cachedURL;
        }

        $info = Http::asJson()
            ->get(sprintf('https://api.weather.gov/points/%f,%f', $lat, $lng));
        
        if ($info->successful()) {
            $data = $info->json()['properties']['forecast'] ?? null;
        }

        if (empty($data)) {
            throw new Exception('Cannot fetch forecast URL', -2);
        }
        
        $url = $data.'?units='.config('weather.weather_gov.unit');
        // store the URL in cache
        Cache::set($cacheKey, $url, $this->urlCacheTTL);

        return $url;
    }

    /**
     * normalize raw json data into a standard format
     *
     * @param array $rawData
     * @return array|Throwable
     */
    protected function normalizeForecastData(array $rawData): array|Throwable
    {
        // example for nomalized data
        $data = [
            'temperature' => (float)$rawData['properties']['periods'][0]['temperature'],
            'temperatureUnit' => $rawData['properties']['periods'][0]['temperatureUnit'],
            'windSpeed' => (float)$rawData['properties']['periods'][0]['windSpeed'],
            'windDirection' => $rawData['properties']['periods'][0]['windDirection'],
            'forecast' => $rawData['properties']['periods'][0]['shortForecast'],
        ];

        return $data;
    }
}