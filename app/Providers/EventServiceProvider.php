<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\LectureCreated;
use App\Events\LectureUpdated;
use App\Events\LectureDeleted;
use App\Listeners\ClearMeetingsCache;
use App\Listeners\SendAnnouncerJoinedNotification;
use App\Listeners\SendLectureDeletionNotification;
use App\Listeners\SendLectureTimeChangedNotification;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LectureCreated::class => [
            ClearMeetingsCache::class,
            SendAnnouncerJoinedNotification::class,
        ],
        LectureUpdated::class => [
            SendLectureTimeChangedNotification::class,
        ],
        LectureDeleted::class => [
            SendLectureDeletionNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
