<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Events;
use Illuminate\Mail\Mailables\Envelope;

class EventMail extends Mailable
{
    use Queueable, SerializesModels;

    public Events $event;

    public function __construct(Events $event)
    {
        $this->event = $event;
    }

    public function build()
    {
        return $this->subject($this->event->titre)
                    ->view('emails.mail_event_template', ['event' => $this->event]);
    }
}

