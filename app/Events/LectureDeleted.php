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

    /**
     * The announcer's email.
     *
     * @var array
     */
    public $emails;

    /**
     * The conference's id.
     *
     * @var int
     */
    public $conferenceId;

    /**
     * The conference's title.
     *
     * @var string
     */
    public $conferenceTitle;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $emails, int $conferenceId, string $conferenceTitle)
    {
        $this->emails = $emails;
        $this->conferenceId = $conferenceId;
        $this->conferenceTitle = $conferenceTitle;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
