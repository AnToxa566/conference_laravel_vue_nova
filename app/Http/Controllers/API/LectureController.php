<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Lecture;

class LectureController extends Controller
{
    protected function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'conference_id' => ['required'],

            'title' => ['required', 'string', 'min:2', 'max:255'],
            'date_time_start' => ['required', 'date'],
            'date_time_end' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'presentation_path' => ['nullable', 'file', 'size:10240', 'mimetypes:application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
    }


    public function fetchAll()
    {
        $lectures = Lecture::all();

        $res = [
            'lectures' => $lectures,
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


    public function destroy($user_id, $conference_id)
    {
        $lecture = Lecture::where('conference_id', $conference_id)->where('user_id', $user_id)->first();

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
