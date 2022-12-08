<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Conference;

class UserConferenceController extends Controller
{
    public function fetchJoinedConferences($userId)
    {
        $conferences = User::find($userId)->conferences()->get()->toArray();
        $conferencesId = array_column($conferences, 'id');

        $res = [
            'conferences_id' => $conferencesId,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function joinConference($userId, $conferenceId)
    {
        $user = User::find($userId);
        $conference = Conference::find($conferenceId);

        $user->conferences()->attach($conference);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function cancelParticipation($userId, $conferenceId)
    {
        $user = User::find($userId);
        $conference = Conference::find($conferenceId);

        $user->conferences()->detach($conference);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
