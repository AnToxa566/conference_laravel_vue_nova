<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\Lecture\LectureStoreRequest;
use App\Http\Requests\Lecture\LectureUpdateRequest;
use App\Http\Requests\Lecture\LectureFetchFilteredRequest;

use App\Events\LectureDeleted;

use App\Mail\AnnouncerJoined;
use App\Mail\LectureTimeChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Lecture;
use App\Models\Conference;

use Illuminate\Http\JsonResponse;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\UserConsts;

class LectureController extends Controller
{
    public function fetchAll(): JsonResponse|string
    {
        return response()->json(Lecture::withCount('comments')->get());
    }


    public function fetchSearchedLectures(string $search, int $limit): JsonResponse
    {
        $response = Lecture::where('title', 'LIKE', '%'.$search.'%')->limit($limit)->get();

        return response()->json($response);
    }


    public function fetchFiltered(LectureFetchFilteredRequest $request): JsonResponse
    {
        $request->validated();

        $conference = Conference::find($request->get('conferenceId'));
        $query = $conference->lectures()->withCount('comments');

        $query->whereRaw('TIMESTAMPDIFF(MINUTE, CAST(date_time_start AS DATETIME), CAST(date_time_end AS DATETIME)) >= ?', [$request->get('minDuration')]);
        $query->whereRaw('TIMESTAMPDIFF(MINUTE, CAST(date_time_start AS DATETIME), CAST(date_time_end AS DATETIME)) <= ?', [$request->get('maxDuration')]);

        if ($request->filled('startTimeAfter')) {
            $query->whereTime('date_time_start', '>=', $request->get('startTimeAfter'));
        }

        if ($request->filled('startTimeBefore')) {
            $query->whereTime('date_time_start', '<=', $request->get('startTimeBefore'));
        }

        if ($request->filled('categoriesId') && count($request->categoriesId)) {
            $query->whereIn('category_id', $request->get('categoriesId'));
        }

        return response()->json($query->get());
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


    public function downloadPresentation(int $id): JsonResponse|BinaryFileResponse
    {
        $query = Lecture::find($id);

        if (!Storage::disk('local')->exists($query->presentation_path)) {
            return response()->json('Error! Please, try again.', 500);
        }

        $path = storage_path('app/' . $query->presentation_path);
        $response = response()->download($path, $query->presentation_name);

        return $response;
    }


    public function store(LectureStoreRequest $request): JsonResponse
    {
        $response = $request->validated();

        $response['presentation_name'] = $request->file('presentation')->getClientOriginalName();
        $response['presentation_path'] = Storage::disk('local')->put('presentations', $request->file('presentation'));

        $lecture = Lecture::create($response);

        $listeners = Conference::find($lecture->conference_id)->users()->where('type', '=', UserConsts::LISTENER)->get();
        if (count($listeners) !== 0) {
            Mail::to($listeners)->send(new AnnouncerJoined($lecture));
        }

        $lecture->{'comments_count'} = 0;
        return response()->json($lecture);
    }


    public function update(LectureUpdateRequest $request, int $id): JsonResponse
    {
        $request->validated();

        $lecture = Lecture::find($id);
        $lecture->date_time_start = $request->date_time_start;
        $lecture->date_time_end = $request->date_time_end;

        $timeChanged = $lecture->isDirty('date_time_start') || $lecture->isDirty('date_time_end');

        $response = tap($lecture)->update($request->toArray());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        if ($timeChanged) {
            $listeners = Conference::find($response->conference_id)->users()->where('type', '=', UserConsts::LISTENER)->get();

            if (count($listeners) !== 0) {
                Mail::to($listeners)->send(new LectureTimeChanged($response));
            }
        }

        $response->{'comments_count'} = count($response->comments);
        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $response = tap(Lecture::find($id))->delete();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        User::find($response->user_id)->conferences()->detach($response->conference_id);

        if (auth('sanctum')->user()->type === UserConsts::ADMIN) {
            $emails = [];
            array_push($emails, User::find($response->user_id)->email);

            LectureDeleted::dispatch($emails, $response->conference->id, $response->conference->title);
        }

        return response()->json($response);
    }
}
