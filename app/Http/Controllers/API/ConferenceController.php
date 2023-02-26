<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ConferenceFetchFilteredRequest;

use App\Models\Conference;
use Illuminate\Http\JsonResponse;


class ConferenceController extends Controller
{
    private function getConferenceAddressById($id): string
    {
        $conference = Conference::findOrFail($id);

        if (!$conference->latitude || !$conference->longitude) {
            return 'Address not set';
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $conference->latitude . ',' . $conference->longitude . '&key=' . config('app.map_key');
        $geocode = file_get_contents($url);
        $json = json_decode($geocode);

        if (count($json->results)) {
            return $json->results[0]->formatted_address;
        }

        return 'undefined';
    }


    public function fetchAll(): JsonResponse
    {
        return response()->json(
            Conference::withCount('lectures')
                    ->beforeEvent()
                    ->oldest('date_time_event')
                    ->get()
        );
    }


    public function fetchDetail(int $id): JsonResponse
    {
        $conference = Conference::findOrFail($id);
        $conference->{'address'} = $this->getConferenceAddressById($id);

        return response()->json($conference);
    }


    public function fetchSearchedConferences(string $search, int $limit): JsonResponse
    {
        return response()->json(Conference::beforeEvent()->search($search, $limit)->get());
    }


    public function fetchFiltered(ConferenceFetchFilteredRequest $request): JsonResponse
    {
        $request->validated();

        $query = Conference::withCount('lectures')->beforeEvent();

        if ($request->filled('minLectureCount')) {
            $query->having('lectures_count', '>=', $request->get('minLectureCount'));
        }

        if ($request->filled('maxLectureCount')) {
            $query->having('lectures_count', '<=', $request->get('maxLectureCount'));
        }

        if ($request->filled('dateAfter')) {
            $query->whereDate('date_time_event', '>=', $request->get('dateAfter'));
        }

        if ($request->filled('dateBefore')) {
            $query->whereDate('date_time_event', '<=', $request->get('dateBefore'));
        }

        if ($request->filled('categoriesId') && count($request->categoriesId)) {
            $query->whereIn('category_id', $request->get('categoriesId'));
        }

        return response()->json($query->oldest('date_time_event')->get());
    }
}
