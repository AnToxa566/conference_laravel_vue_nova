<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\ZoomMeeting;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;


class ZoomMeetingPolicy
{
    use HandlesAuthorization;


    public function view(User $user, ZoomMeeting $zoomMeeting): Response|bool
    {
        return true;
    }
}
