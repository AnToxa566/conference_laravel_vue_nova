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

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ZoomMeeting $zoomMeeting): Response|bool
    {
        return true;
    }
}
