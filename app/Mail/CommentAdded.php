<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Comment;


class CommentAdded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New comment added to your lecture',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.comment.added',

            with: [
                'userName' => $this->comment->user->first_name . ' ' . $this->comment->user->last_name,

                'lectureId' => $this->comment->lecture->id,
                'lectureTitle' => $this->comment->lecture->title,

                'conferenceId' => $this->comment->lecture->conference->id,
                'conferenceTitle' => $this->comment->lecture->conference->title,
            ],
        );
    }
}
