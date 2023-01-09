<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LectureDeletedByAdmin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The conference's id.
     *
     * @var int
     */
    public int $conferenceId;

    /**
     * The conference's title.
     *
     * @var string
     */
    public string $conferenceTitle;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $conferenceId, string $conferenceTitle)
    {
        $this->conferenceId = $conferenceId;
        $this->conferenceTitle = $conferenceTitle;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lecture has been deleted by administrator',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.lecture.admin.deleted',

            with: [
                'conferenceId' => $this->conferenceId,
                'conferenceTitle' => $this->conferenceTitle,
            ],
        );
    }
}
