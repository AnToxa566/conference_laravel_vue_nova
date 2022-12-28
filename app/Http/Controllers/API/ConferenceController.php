<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ConferenceStoreRequest;
use App\Http\Requests\Conference\ConferenceUpdateRequest;
use App\Http\Requests\Conference\ConferenceFetchFilteredRequest;

use App\Models\Conference;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        $response = Conference::withCount('lectures')->get();

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


    public function fetchSearchedConferences(string $search, int $limit): JsonResponse
    {
        $response = Conference::where('title', 'LIKE', '%'.$search.'%')->limit($limit)->get();

        return response()->json($response);
    }


    public function fetchFiltered(ConferenceFetchFilteredRequest $request): JsonResponse
    {
        $request->validated();

        $query = Conference::withCount('lectures');

        $query->having('lectures_count', '>=', $request->get('minLectureCount'));
        $query->having('lectures_count', '<=', $request->get('maxLectureCount'));

        if ($request->filled('dateAfter')) {
            $query->whereDate('date_time_event', '>=', $request->get('dateAfter'));
        }

        if ($request->filled('dateBefore')) {
            $query->whereDate('date_time_event', '<=', $request->get('dateBefore'));
        }

        if ($request->filled('categoriesId') && count($request->categoriesId)) {
            $query->whereIn('category_id', $request->get('categoriesId'));
        }

        return response()->json($query->get());
    }


    public function store(ConferenceStoreRequest $request): JsonResponse
    {
        $response = Conference::create($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'lectures_count'} = 0;

        return response()->json($response);
    }


    public function update(ConferenceUpdateRequest $request, int $id): JsonResponse
    {
        $response = tap(Conference::find($id))->update($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'lectures'} = $response->lectures;
        $response->{'lectures_count'} = count($response->lectures);

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
