<?php

namespace Tests\Unit\Services;

use App\Services\Weather\OpenWeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenWeatherServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that currentForecast returns a valid response
     *
     * @return void
     */
    public function test_currentForecast_returns_valid_response()
    {
        // Arrange
        $latitude = $this->faker->latitude();
        $longitude = $this->faker->longitude();
        $mockResponse = [
            'main' => [
                'temp' => 20.0,
            ],
            'wind' => [
                'speed' => 5.0,
                'deg' => 180,
            ],
            'weather' => [
                [
                    'main' => 'Cloudy',
                ],
            ],
        ];
        Http::fake([
            '*' => Http::response($mockResponse),
        ]);

        $config = [
            'url' => 'https://fake-weather-api.com?lat=%s&lon=%s&lang=%s&units=%s&appid=%s',
            'lang' => 'en',
            'unit' => 'metric',
            'api_key' => env('OPENWAETHER_API_KEY'),
        ];
        $openWeatherService = new OpenWeatherService($config);

        // Act
        $result = $openWeatherService->currentForecast($latitude, $longitude);

        // Assert
        $this->assertEquals($result['temperature'], $mockResponse['main']['temp']);
        $this->assertEquals($result['temperatureUnit'], 'C');
        $this->assertEquals($result['windSpeed'], $mockResponse['wind']['speed']);
        $this->assertEquals($result['windDirection'], 'S');
        $this->assertEquals($result['forecast'], $mockResponse['weather'][0]['main']);
    }

    /**
     * Test that currentForecast throws an exception when unable to fetch data
     *
     * @return void
     */
    public function test_currentForecast_throws_exception_when_unable_to_fetch_data()
    {
        // Arrange
        $latitude = $this->faker->latitude();
        $longitude = $this->faker->longitude();
        Http::fake([
            '*' => Http::response([], 404),
        ]);

        $config = [
            'url' => 'https://fake-weather-api.com?lat=%s&lon=%s&lang=%s&units=%s&appid=%s',
            'lang' => 'en',
            'unit' => 'metric',
            'api_key' => env('OPENWAETHER_API_KEY'),
        ];
        $openWeatherService = new OpenWeatherService($config);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can not fetch forecast data');

        // Act
        $openWeatherService->currentForecast($latitude, $longitude);
    }
}
