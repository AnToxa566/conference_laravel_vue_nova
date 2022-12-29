<?php

namespace App\Mail;

use App\Models\Lecture;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncerJoined extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The lecture instance.
     *
     * @var \App\Models\Lecture
     */
    public $lecture;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Lecture $lecture)
    {
        $this->lecture = $lecture;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'A new announcer has joined the conference',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.announcer.joined',

            with: [
                'announcerName' => $this->lecture->user->first_name . ' ' . $this->lecture->user->last_name,

                'conferenceId' => $this->lecture->conference->id,
                'conferenceTitle' => $this->lecture->conference->title,

                'lectureId' => $this->lecture->id,
                'lectureTitle' => $this->lecture->title,
                'lectureStartTime' => Carbon::parse($this->lecture->date_time_start)->format('H:i'),
                'lectureEndTime' => Carbon::parse($this->lecture->date_time_end)->format('H:i'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
