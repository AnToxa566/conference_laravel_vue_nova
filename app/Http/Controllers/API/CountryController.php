<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Models\Country;

class CountryController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        $response = Country::all();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }
}
