<?php

namespace Tests\Unit\Services\Weather;

use Tests\TestCase;
use App\Services\Weather\WeatherGovService;
use Illuminate\Support\Facades\Http;

class WeatherGovServiceTest extends TestCase
{
    /**
     * Test currentForecast method
     *
     * @return void
     */
    public function testCurrentForecast()
    {
        // Mock response from weather.gov
        $weatherData = [
            'properties' => [
                'periods' => [
                    [
                        'temperature' => 18,
                        'temperatureUnit' => 'C',
                        'windSpeed' => 5,
                        'windDirection' => 'SW',
                        'shortForecast' => 'Mostly Cloudy',
                    ]
                ]
            ]
        ];

        Http::fake([
            '*' => Http::response($weatherData, 200)
        ]);

        $service = new WeatherGovService();

        // // Test with valid latitude and longitude
        // $result = $service->currentForecast(37.7749, -122.4194);

        // $this->assertIsArray($result);
        // $this->assertEquals(18, $result['temperature']);
        // $this->assertEquals('C', $result['temperatureUnit']);
        // $this->assertEquals(5, $result['windSpeed']);
        // $this->assertEquals('SW', $result['windDirection']);
        // $this->assertEquals('Mostly Cloudy', $result['forecast']);

        // Test with invalid latitude and longitude
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot fetch forecast URL');
        $service->currentForecast(1000, 1000);

        // Test with unsuccessful request
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can not fetch forecast data');
        $service->currentForecast(37.7749, -122.4194);
    }
}
