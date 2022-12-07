<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Conference;

class UserConferenceController extends Controller
{
    public function fetchJoinedConferences($user_id)
    {
        $conferences = User::find($user_id)->conferences()->get()->toArray();
        $conferences_id = array_column($conferences, 'id');

        $res = [
            'conferences_id' => $conferences_id,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function joinConference($user_id, $conference_id)
    {
        $user = User::find($user_id);
        $conference = Conference::find($conference_id);

        $user->conferences()->attach($conference);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function cancelParticipation($user_id, $conference_id)
    {
        $user = User::find($user_id);
        $conference = Conference::find($conference_id);

        $user->conferences()->detach($conference);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
