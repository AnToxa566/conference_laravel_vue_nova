<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

use App\Mail\AnnouncerJoined;
use App\Events\LectureCreated;

use App\Models\User;
use App\Models\Conference;


class SendAnnouncerJoinedNotification
{
    public function handle(LectureCreated $event): void
    {
        $listeners = Conference::findOrFail($event->lecture->conference_id)->users()->where('type', User::LISTENER)->get();

        if ($listeners->isNotEmpty()) {
            Mail::to($listeners)->send(new AnnouncerJoined($event->lecture));
        }
    }
}
