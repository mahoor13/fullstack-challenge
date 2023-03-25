<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserController extends Controller
{
    /**
     * List all users
     * 
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $data = User::select('id', 'name', 'email', 'latitude', 'longitude')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 0,
            'message' => null,
            'data' => $data,
        ], 200);
    }
}