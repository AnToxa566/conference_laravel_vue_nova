<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ZoomMeetingTrait;
use App\Models\ZoomMeeting;
use App\Models\Lecture;

use DateTime;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class MeetingController extends Controller
{
    use ZoomMeetingTrait;


    public function fetchAll(): JsonResponse
    {
        return response()->json(ZoomMeeting::all());
    }


    public function store(int $lectureId): JsonResponse
    {
        $lecture = Lecture::find($lectureId);

        if (!$lecture) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = json_decode($this->createMeeting($lecture)->getData(), true);
        $response['lecture_id'] = $lectureId;
        $response['start_time'] = (new DateTime($response['start_time']))->format('Y-m-d H:i:s');

        $zoomMeeting = ZoomMeeting::create($response);

        if (!$zoomMeeting) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($zoomMeeting);
    }
}
