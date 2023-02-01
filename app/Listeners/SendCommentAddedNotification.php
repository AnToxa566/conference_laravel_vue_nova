<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Mail\CommentAdded;
use App\Events\CommentCreated;
use Illuminate\Support\Facades\Mail;


class SendCommentAddedNotification
{
    public function handle(CommentCreated $event): void
    {
        Mail::to($event->comment->lecture->user)->send(new CommentAdded($event->comment));
    }
}
