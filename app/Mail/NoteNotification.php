<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $note;
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct($note, $action)
    {
        $this->note = $note;
        $this->action = $action;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->action) {
            'created' => 'Note Created: ' . ($this->note->title ?? ''),
            'updated' => 'Note Updated: ' . ($this->note->title ?? ''),
            'deleted' => 'Note Deleted: ' . ($this->note->title ?? ''),
            default => 'Note Notification',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.note-notification',
            with: [
                'note' => $this->note,
                'action' => $this->action,
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
