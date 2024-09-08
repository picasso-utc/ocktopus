<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailPerm extends Mailable
{
    use Queueable, SerializesModels;

    public $record;

    /**
     * Create a new message instance.
     */
    public function __construct($record)
    {
        $this->record = $record;
        $this->creneaux = $record->creneaux;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Information sur votre permanence',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mail_perm_template',
            with: [
                'record' => $this->record,
                'creneaux' => $this->creneaux,  // Passez les créneaux ici
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
