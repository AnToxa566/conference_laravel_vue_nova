<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\Plan;

use App\Events\LectureCreated;


class JoinAnnouncerToConference
{
    public function handle(LectureCreated $event): void
    {
        $user = User::findOrFail($event->lecture->user_id);

        $user->conferences()->attach($event->lecture->conference_id);

        if ($user->joins_left !== Plan::UNLIMITED_JOINS) {
            $user->decrement('joins_left');
        }
    }
}
