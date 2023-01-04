<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use App\Models\Country;

class CountryController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        return response()->json(Country::all());
    }
}
