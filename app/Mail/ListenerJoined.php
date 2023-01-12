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
use App\Models\User;

class ListenerJoined extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

     /**
     * The conference instance.
     *
     * @var \App\Models\Conference
     */
    public Conference $conference;

    /**
     * A new listener.
     *
     * @var \App\Models\User
     */
    public User $listener;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $listener, Conference $conference)
    {
        $this->listener = $listener;
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
            subject: 'A new listener has joined the conference',
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
            markdown: 'emails.listener.joined',

            with: [
                'listenerName' => $this->listener->first_name . ' ' . $this->listener->last_name,

                'conferenceId' => $this->conference->id,
                'conferenceTitle' => $this->conference->title,
            ],
        );
    }
}
