<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Conference;

class ConferenceDeleted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The conference instance.
     *
     * @var \App\Models\Conference
     */
    public $conference;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
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
                'conferenceTitle' => $this->conference->title,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
