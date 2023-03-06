<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'all systems are a go',
        'users' => \App\Models\User::all(),
    ]);
});


/*
|--------------------------------------------------------------------------
| API Version 1
|--------------------------------------------------------------------------
|
| We use versioning for APIs to manage changes in the API's functionality
| and behavior over time. Versioning allows API clients to know exactly
| which version of the API they are using and to adjust their code
| accordingly. It also enables API developers to make changes to the API
| without breaking existing clients.
|
*/

Route::prefix('v1')->group(function () {

    // User Controller
    Route::prefix('user')->group(
        function () {
            Route::get('list', [V1\UserController::class, 'list'])
                ->name('user.list');
        }
    );

    // Weather Controller
    Route::prefix('weather')->group(
        function () {
            Route::get('list', [V1\WeatherController::class, 'list'])
                ->name('weather.list');

            Route::get('detail/{userId}', [V1\WeatherController::class, 'detail'])
                ->where('userId', '[0-9]+')
                ->name('weather.detail');
        }
    );
});