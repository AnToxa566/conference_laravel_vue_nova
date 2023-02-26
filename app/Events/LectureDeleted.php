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

class LectureDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $emails;

    public int $conferenceId;

    public string $conferenceTitle;

    public function __construct(array $emails, int $conferenceId, string $conferenceTitle)
    {
        $this->emails = $emails;
        $this->conferenceId = $conferenceId;
        $this->conferenceTitle = $conferenceTitle;
    }
}
