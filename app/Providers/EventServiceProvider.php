<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\CommentCreated;
use App\Events\LectureCreated;
use App\Events\LectureUpdated;
use App\Events\LectureDeleted;
use App\Listeners\ClearMeetingsCache;
use App\Listeners\JoinAnnouncerToConference;
use App\Listeners\SendCommentAddedNotification;
use App\Listeners\SendAnnouncerJoinedNotification;
use App\Listeners\SendLectureDeletionNotification;
use App\Listeners\SendLectureTimeChangedNotification;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CommentCreated::class => [
            SendCommentAddedNotification::class,
        ],
        LectureCreated::class => [
            ClearMeetingsCache::class,
            JoinAnnouncerToConference::class,
            SendAnnouncerJoinedNotification::class,
        ],
        LectureUpdated::class => [
            SendLectureTimeChangedNotification::class,
        ],
        LectureDeleted::class => [
            SendLectureDeletionNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
