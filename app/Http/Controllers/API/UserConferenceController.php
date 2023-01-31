<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\Conference\ConferenceJoinRequest;
use App\Http\Requests\Conference\ConferenceCancelParticipationRequest;

use App\Mail\ListenerJoined;

use App\Models\User;
use App\Models\Plan;
use App\Models\Conference;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;


class UserConferenceController extends Controller
{
    public function fetchJoinedConferences(int $userId): JsonResponse
    {
        return response()->json(array_column(User::findOrFail($userId)->conferences()->get()->toArray(), 'id'));
    }


    public function joinConference(ConferenceJoinRequest $request): JsonResponse
    {
        $conferenceId = $request->validated()['conferenceId'];
        $user = $request->user();

        $user->conferences()->attach($conferenceId);

        if ($user->joins_left !== Plan::UNLIMITED_JOINS) {
            $user->decrement('joins_left');
        }

        if ($user->type === User::LISTENER) {
            $conference = Conference::findOrFail($conferenceId);
            $announcers = $conference->users()->where('type', User::ANNOUNCER)->get();

            if ($announcers->isNotEmpty()) {
                Mail::to($announcers)->send(new ListenerJoined($user, $conference));
            }
        }

        return response()->json(null, 204);
    }


    public function cancelParticipation(ConferenceCancelParticipationRequest $request): JsonResponse
    {
        $request->user()->conferences()->detach($request->validated()['conferenceId']);

        return response()->json(null, 204);
    }
}
