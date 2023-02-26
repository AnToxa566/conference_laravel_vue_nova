<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Http\Requests\Lecture\LectureStoreRequest;
use App\Http\Requests\Lecture\LectureUpdateRequest;
use App\Http\Requests\Lecture\LectureFetchFilteredRequest;

use App\Events\LectureCreated;
use App\Events\LectureUpdated;

use App\Models\Lecture;
use App\Models\Conference;


class LectureController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        return response()->json(
            Lecture::withCount('comments')
                ->oldest('date_time_start')
                ->get()
        );
    }


    public function fetchSearchedLectures(string $search, int $limit): JsonResponse
    {
        return response()->json(Lecture::beforeConferenceEvent()->search($search, $limit)->get());
    }


    public function fetchFiltered(LectureFetchFilteredRequest $request): JsonResponse
    {
        $request->validated();

        $conference = Conference::findOrFail($request->get('conferenceId'));
        $query = $conference->lectures()->withCount('comments');

        if ($request->filled('minDuration')) {
            $query->whereRaw('TIMESTAMPDIFF(MINUTE, CAST(date_time_start AS DATETIME), CAST(date_time_end AS DATETIME)) >= ?', [$request->get('minDuration')]);
        }

        if ($request->filled('maxDuration')) {
            $query->whereRaw('TIMESTAMPDIFF(MINUTE, CAST(date_time_start AS DATETIME), CAST(date_time_end AS DATETIME)) <= ?', [$request->get('maxDuration')]);
        }

        if ($request->filled('startTimeAfter')) {
            $query->whereTime('date_time_start', '>=', $request->get('startTimeAfter'));
        }

        if ($request->filled('startTimeBefore')) {
            $query->whereTime('date_time_start', '<=', $request->get('startTimeBefore'));
        }

        if ($request->filled('categoriesId') && count($request->categoriesId)) {
            $query->whereIn('category_id', $request->get('categoriesId'));
        }

        return response()->json($query->oldest('date_time_start')->get());
    }


    public function fetchById(int $id): JsonResponse
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->{'comments_count'} = count($lecture->comments);

        return response()->json($lecture);
    }


    public function downloadPresentation(int $id): JsonResponse|BinaryFileResponse
    {
        $lecture = Lecture::findOrFail($id);

        if (!Storage::disk('local')->exists($lecture->presentation_path)) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return response()->download(storage_path('app/' . $lecture->presentation_path), $lecture->presentation_name);;
    }


    public function store(LectureStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['presentation_name'] = $request->file('presentation')->getClientOriginalName();
        $validated['presentation_path'] = Storage::disk('local')->put('presentations', $request->file('presentation'));

        $createdLecture = Lecture::create($validated);

        if (!$createdLecture) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        LectureCreated::dispatch($createdLecture);

        $meeting = $createdLecture->is_online ? (new MeetingController)->store($createdLecture->id) : null;
        $createdLecture->{'comments_count'} = 0;

        return response()->json([
            'lecture' => $createdLecture,
            'meeting' => $meeting ? $meeting->original : null,
        ]);
    }


    public function update(LectureUpdateRequest $request, int $id): JsonResponse
    {
        $updatedLecture = tap(Lecture::findOrFail($id))->update($request->validated());

        LectureUpdated::dispatch($updatedLecture);

        $updatedLecture->{'comments_count'} = count($updatedLecture->comments);
        return response()->json($updatedLecture);
    }


    public function destroy(int $id): JsonResponse
    {
        $deletedLecture = tap(Lecture::findOrFail($id))->delete();

        $deletedLecture->user->conferences()->detach($deletedLecture->conference_id);

        return response()->json($deletedLecture);
    }
}
