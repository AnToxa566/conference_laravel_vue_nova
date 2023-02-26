<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\LectureUpdated;

use App\Mail\LectureTimeChanged;
use App\Models\Conference;
use App\Models\User;

class SendLectureTimeChangedNotification
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\LectureUpdated  $event
     * @return void
     */
    public function handle(LectureUpdated $event): void
    {
        if ($event->lecture->wasChanged(['date_time_start', 'date_time_end'])) {
            $listeners = Conference::findOrFail($event->lecture->conference_id)->users()->where('type', User::LISTENER)->get();

            if ($listeners->isNotEmpty()) {
                Mail::to($listeners)->send(new LectureTimeChanged($event->lecture));
            }
        }
    }
}
