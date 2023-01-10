<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ConferenceStoreRequest;
use App\Http\Requests\Conference\ConferenceUpdateRequest;
use App\Http\Requests\Conference\ConferenceFetchFilteredRequest;

use App\Jobs\ExportFile;
use App\Events\LectureDeleted;

use App\Mail\ConferenceDeleted;
use Illuminate\Support\Facades\Mail;

use App\Exports\AllConferencesExport;
use App\Exports\ListenersByConferenceExport;

use App\Models\Conference;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    private function getConferenceAddressById($id): string
    {
        $conference = Conference::find($id);

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
        $response = Conference::find($id);

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $response->{'address'} = $this->getConferenceAddressById($id);

        return response()->json($response);
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


    public function store(ConferenceStoreRequest $request): JsonResponse
    {
        $response = Conference::create($request->validated());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response->{'lectures_count'} = 0;

        return response()->json($response);
    }


    public function update(ConferenceUpdateRequest $request, int $id): JsonResponse
    {
        $response = tap(Conference::find($id))->update($request->validated());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $response->{'lectures'} = $response->lectures;
        $response->{'lectures_count'} = count($response->lectures);

        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $users = Conference::find($id)->users;
        $lectures = Conference::find($id)->lectures;
        $response = tap(Conference::find($id))->delete();

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        if (count($users)) {
            Mail::to($users)->send(new ConferenceDeleted($response->title));
        }

        if (count($lectures)) {
            $emails = [];

            foreach ($lectures as $lecture) {
                array_push($emails, $lecture->user->email);
            }

            LectureDeleted::dispatch($emails, $response->id, $response->title);
        }

        return response()->json($response);
    }


    public function exportAll(): void
    {
        ExportFile::dispatch('conferences.csv', new AllConferencesExport());
    }


    public function exportListeners(int $conferenceId): void
    {
        ExportFile::dispatch('c'.$conferenceId.'_listeners.csv', new ListenersByConferenceExport($conferenceId));
    }
}
