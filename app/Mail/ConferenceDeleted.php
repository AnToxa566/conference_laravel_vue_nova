<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConferenceDeleted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The conference instance.
     *
     * @var string
     */
    public string $conferenceTitle;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $conferenceTitle)
    {
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
            subject: 'Conference has been deleted',
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
            markdown: 'emails.conference.deleted',

            with: [
                'conferenceTitle' => $this->conferenceTitle,
            ],
        );
    }
}
