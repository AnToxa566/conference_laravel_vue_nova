<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

use App\Events\LectureCreated;

class ClearMeetingsCache
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\LectureCreated  $event
     * @return void
     */
    public function handle(LectureCreated $event): void
    {
        Cache::forget('meetings');
    }
}
