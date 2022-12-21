<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lecture\LectureStoreRequest;
use App\Http\Requests\Lecture\LectureUpdateRequest;
use Illuminate\Support\Facades\Storage;

use App\Models\Lecture;
use Illuminate\Http\JsonResponse;

class LectureController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        $response = Lecture::all();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        foreach ($response as $value) {
            $value->{'comments_count'} = count($value->comments);
        }

        return response()->json($response);
    }


    public function fetchById(int $id): JsonResponse
    {
        $response = Lecture::find($id);

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'comments_count'} = count($response->comments);

        return response()->json($response);
    }


    public function store(LectureStoreRequest $request): JsonResponse
    {
        $response = $request->validated();

        if ($request->hasFile('presentation')) {
            $presentation_name = $request->file('presentation')->getClientOriginalName();
            $presentation_path = Storage::disk('local')->put('presentations', $request->file('presentation'));

            $response['presentation_name'] = $presentation_name;
            $response['presentation_path'] = $presentation_path;
        }

        $response = Lecture::create($response);

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }


    public function update(LectureUpdateRequest $request, int $id): JsonResponse
    {
        $response = tap(Lecture::find($id))->update($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $response = tap(Lecture::find($id))->delete();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        return response()->json($response);
    }
}
