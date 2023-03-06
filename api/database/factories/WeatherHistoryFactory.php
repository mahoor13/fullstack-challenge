<?php 

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeatherHistory>
 */
class WeatherHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'weather_service' => fake()->randomElement(['open_weather', 'weather_gov']),
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
            'weather_info' => json_encode([
                'temperature' => fake()->numberBetween(-20, 40),
                'humidity' => fake()->numberBetween(0, 100),
                'pressure' => fake()->numberBetween(900, 1100),
                'wind_speed' => fake()->numberBetween(0, 20),
                'weather_condition' => fake()->randomElement(['Sunny', 'Cloudy', 'Rainy', 'Windy']),
            ]),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];;
    }
}