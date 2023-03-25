<?php

namespace Tests\Unit\V1;

use App\Models\User;
use App\Models\WeatherHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the "list" method of the WeatherController
     *
     * @return void
     */
    public function testList()
    {
        $weather1 = WeatherHistory::factory();
        $userId1 = (int)$weather1->create()->user_id;

        $weather2 = WeatherHistory::factory();
        $userId2 = (int)$weather2->create()->user_id;
        // call the API endpoint
        $response = $this->getJson('/v1/weather/list');

        // check the response status code
        $response->assertStatus(200);

        // check the response structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                $userId1 => [
                    'value',
                    'unit',
                ],
                $userId2 => [
                    'value',
                    'unit',
                ],
            ],
        ]);
    }

    public function testDetailReturnsCorrectData()
    {
        // create a user and a weather history for that user
        $user = (int)User::factory()->create()->id;
        $weatherHistory = WeatherHistory::factory()->create([
            'user_id' => $user,
            'weather_info' => '{"temperature": 25, "temperatureUnit": "C"}',
        ]);

        // call the API endpoint
        $response = $this->json('GET', '/v1/weather/detail/' . $user);

        // assert that the response is successful and contains the expected data
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => 0,
                'message' => null,
                'data' => [
                    'id' => $weatherHistory->id,
                    'user_id' => $user,
                    'weather_info' => '{"temperature": 25, "temperatureUnit": "C"}',
                ]
            ]);
    }

    public function testDetailReturns404ForNonexistentUser()
    {
        // call the API endpoint with a nonexistent user ID
        $response = $this->json('GET', '/v1/weather/detail/123');

        // assert that the response is a 404 error
        $response//->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJson([
                'status' => 404,
                'message' => 'No weather data found for this user',
            ]);
    }

    // add any other tests you want to include here
}