<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Lecture;


class LectureCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public Lecture $lecture;


    public function __construct(Lecture $lecture)
    {
        $this->lecture = $lecture;
    }
}
