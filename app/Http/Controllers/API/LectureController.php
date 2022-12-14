<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Lecture;

class LectureController extends Controller
{
    protected function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'conference_id' => ['required'],
            'category_id' => ['nullable'],

            'title' => ['required', 'string', 'min:2', 'max:255'],
            'date_time_start' => ['required', 'date'],
            'date_time_end' => ['required', 'date'],
            'description' => ['string'],
            'presentation_path' => ['file', 'size:10240', 'mimetypes:application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
    }


    public function fetchAll()
    {
        $lectures = Lecture::leftJoin('comments', 'lectures.id', '=', 'comments.lecture_id')
                        ->select('lectures.*', DB::raw('count(comments.lecture_id) as comments_count'))
                        ->groupBy('lectures.id')
                        ->get();

        $res = [
            'lectures' => $lectures,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function fetchById($id)
    {
        $lecture = Lecture::where('id', $id)->first();

        if (!$lecture) {
            return response()->json(['error' => 'LectureController::fetchById: Lecture with the given id were not found.'], 404);
        }

        $res = [
            'lecture' => $lecture,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function store(Request $request)
    {
        LectureController::validation($request);

        $input = $request->all();

        if ($request->hasFile('presentation_path')) {
            $path = $request->file('presentation_path')->store('presentations');
            $input['presentation_path'] = $path;
        }

        $lecture = Lecture::create($input);

        $res = [
            'lecture' => $lecture,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function update(Request $request, $id)
    {
        $lecture = Lecture::where('id', $id)->first();

        if (!$lecture) {
            return response()->json(['error' => 'LectureController::update: Lecture with the given id were not found.'], 404);
        }

        LectureController::validation($request);

        $input = $request->all();

        if ($request->hasFile('presentation_path')) {
            $path = $request->file('presentation_path')->store('presentations');
            $input['presentation_path'] = $path;
        }

        $input['category_id'] = $input['category_id'] ? $input['category_id'] : null;

        $lecture->update($input);

        $res = [
            'lecture' => $lecture,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function destroy($userId, $conferenceId)
    {
        $lecture = Lecture::where('conference_id', $conferenceId)->where('user_id', $userId)->first();

        if (!$lecture) {
            return response()->json(['error' => 'LectureController::destroy: Lecture with the given id were not found.'], 401);
        }

        $lecture->delete();

        $res = [
            'lecture_id' => $lecture->id,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
