<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ConferenceStoreRequest;
use App\Http\Requests\Conference\ConferenceUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Conference;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        $response = Conference::all();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }


    public function fetchDetail(int $id): JsonResponse
    {
        $response = Conference::find($id);

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }


    public function store(ConferenceStoreRequest $request): JsonResponse
    {
        $response = Conference::create($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }


    public function update(ConferenceUpdateRequest $request, int $id): JsonResponse
    {
        $response = tap(Conference::find($id))->update($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'lectures'} = $response->lectures;

        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $response = Conference::find($id)->delete();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }
}
