<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Conference;

class ConferenceController extends Controller
{
    protected function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'date_time_event' => ['required', 'date', 'after_or_equal:today'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
    }

    public function fetchAll()
    {
        $conferences = Conference::all();

        if (!$conferences) {
            return response()->json(['error' => 'Failed to receive conferences.'], 401);
        }

        $res = [
            'conferences' => $conferences,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function fetchDetail($id)
    {
        $conference = Conference::where('id', $id)->first();

        if (!$conference) {
            return response()->json(['error' => 'Conference with the given id were not found.'], 401);
        }

        $res = [
            'conference' => $conference,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function store(Request $request)
    {
        ConferenceController::validation($request);

        $input = $request->all();
        $input['latitude'] = $input['longitude'] == null ? null : $input['latitude'];
        $input['longitude'] = $input['latitude'] == null ? null : $input['longitude'];

        $conference = Conference::create($input);

        $res = [
            'conference' => $conference,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function edit($id)
    {
        $conference = Conference::where('id', $id)->first();

        if (!$conference) {
            return response()->json(['error' => 'Conference with the given id were not found.'], 401);
        }

        $res = [
            'conference' => $conference,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function update(Request $request, $id)
    {
        $conference = Conference::where('id', $id)->first();

        if (!$conference) {
            return response()->json(['error' => 'Conference with the given id were not found.'], 401);
        }

        ConferenceController::validation($request);

        $input = $request->all();
        $input['latitude'] = $input['longitude'] == null ? null : $input['latitude'];
        $input['longitude'] = $input['latitude'] == null ? null : $input['longitude'];

        $conference->update($input);

        $res = [
            'conference' => $conference,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function destroy($id)
    {
        $conference = Conference::where('id', $id)->first();

        if (!$conference) {
            return response()->json(['error' => 'Conference with the given id were not found.'], 401);
        }

        $conference->delete();

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
