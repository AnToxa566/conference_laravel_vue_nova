<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ZoomMeetingTrait;
use App\Models\ZoomMeeting;
use App\Models\Lecture;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MeetingController extends Controller
{
    use ZoomMeetingTrait;


    public function fetchAllFromAPI(): JsonResponse
    {
        return response()->json(
            Cache::rememberForever('meetings', function () {
                return $this->getMeetings();
            })
        );
    }


    public function fetchAllFromDB(): JsonResponse
    {
        return response()->json(ZoomMeeting::all());
    }


    public function store(int $lectureId): JsonResponse
    {
        $zoomMeeting = $this->createMeeting(Lecture::findOrFail($lectureId));
        $zoomMeeting['lecture_id'] = $lectureId;

        $createdZoomMeeting = ZoomMeeting::create($zoomMeeting);

        if (!$createdZoomMeeting) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($createdZoomMeeting);
    }


    public function update(int $meetingId): JsonResponse
    {
        $lectureId = ZoomMeeting::findOrFail($meetingId)->lecture_id;

        $zoomMeeting = $this->updateMeeting($meetingId, Lecture::findOrFail($lectureId));
        $zoomMeeting['lecture_id'] = $lectureId;

        $updatedZoomMeeting = tap(ZoomMeeting::findOrFail($meetingId))->update($zoomMeeting);

        if (!$updatedZoomMeeting) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($updatedZoomMeeting);
    }
}
