<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use App\Mail\LectureDeletedByAdmin;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LectureDeleted;

class SendLectureDeletionNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LectureDeleted  $event
     * @return void
     */
    public function handle(LectureDeleted $event)
    {
        Mail::to($event->emails)->send(new LectureDeletedByAdmin($event->conferenceId, $event->conferenceTitle));
    }
}
