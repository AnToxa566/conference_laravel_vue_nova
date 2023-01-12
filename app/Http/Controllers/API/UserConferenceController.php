<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;

use App\Mail\ListenerJoined;
use Illuminate\Support\Facades\Mail;

class UserConferenceController extends Controller
{
    public function fetchJoinedConferences(int $userId): JsonResponse
    {
        return response()->json(array_column(User::find($userId)->conferences()->get()->toArray(), 'id'));
    }


    public function joinConference(int $userId, int $conferenceId): void
    {
        $user = User::find($userId);
        $user->conferences()->attach($conferenceId);

        if ($user->type === User::LISTENER) {
            $announcers = Conference::find($conferenceId)->users()->where('type', User::ANNOUNCER)->get();

            if (count($announcers)) {
                Mail::to($announcers)->send(new ListenerJoined($user, Conference::find($conferenceId)));
            }
        }
    }


    public function cancelParticipation($userId, $conferenceId): void
    {
        User::find($userId)->conferences()->detach($conferenceId);
    }
}
