<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\WeatherHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;

class WeatherController extends Controller
{
    /**
     * returns simple weather information for all users
     * 
     * @return JsonResponse
     */
    public function list(WeatherService $weatherService): JsonResponse
    {
        // get list of latest temperatures for each user. the array key is user_id
        $data = DB::table('users')
            ->select('users.id', 'weather_info')
            ->join(DB::raw('(SELECT user_id, MAX(created_at) AS max_created_at FROM weather_histories GROUP BY user_id) latest_weather_histories'), function($join) {
                $join->on('users.id', '=', 'latest_weather_histories.user_id');
            })
            ->join('weather_histories', function($join) {
                $join->on('latest_weather_histories.user_id', '=', 'weather_histories.user_id');
                $join->on('latest_weather_histories.max_created_at', '=', 'weather_histories.created_at');
            })
            ->get()
            ->map(function ($row) {
                $weatherInfo = json_decode($row->weather_info);
                return [
                    'user_id' => $row->id,
                    'temperature' => [
                        'value' => $weatherInfo->temperature ?? null,
                        'unit' => $weatherInfo->temperatureUnit ?? null,
                    ],
                ];
            })
            ->pluck('temperature', 'user_id');

        // no records
        if (empty($data)) {
            return response()->json([
                'status' => 404,
                'message' => 'Weather history is empty',
            ], 404);
        }
        // success
        return response()->json([
            'status' => 0,
            'message' => null,
            'data' => $data
        ], 200);
    }

    /**
     * Detailed weather information for a single user
     * 
     * @param int $userId
     * @return JsonResponse
     */
    public function detail(int $userId): JsonResponse
    {
        $data = WeatherHistory::where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();
        // no records for this userId
        if (empty($data)) {
            return response()->json([
                'status' => 404,
                'message' => 'No weather data found for this user',
            ], 404);
        }
        $data->last_update = Carbon::parse($data->created_at)->format('l, F jS, Y \a\t h:i A T');
        $data->weather_service = ucwords(str_replace('_', ' ', $data->weather_service));
        // success
        return response()->json([
            'status' => 0,
            'message' => null,
            'data' => $data,
        ], 200);
    }
}