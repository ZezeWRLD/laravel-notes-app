<?php

namespace App\Mail;

use App\Models\Note;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NoteSharedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Note $note,
        public User $sender,
        public User $recipient,
        public string $accessLevel
    ) {}
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
           subject: $this->sender->name . ' shared "' . $this->note->title . '" with you',
            tags: ['note-shared', 'notification'],
            metadata: [
                'note_id' => $this->note->id,
                'sender_id' => $this->sender->id,
                'recipient_id' => $this->recipient->id,
                'access_level' => $this->accessLevel,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
     public function content(): Content
    {
        $template = \App\Models\EmailTemplate::where('slug', 'note_shared')->first();

        if ($template) {
            $rendered = $template->render([
                'sender_name' => $this->sender->name,
                'recipient_name' => $this->recipient->name,
                'note_title' => $this->note->title,
                'note_excerpt' => str_limit($this->note->content, 150),
                'note_url' => URL::route('notes.show', $this->note),
                'access_level' => ucfirst($this->accessLevel),
                'unsubscribe_url' => $this->recipient->getUnsubscribeUrl(),
                'settings_url' => URL::route('profile.email-settings'),
                'app_name' => config('app.name'),
                'year' => date('Y'),
            ]);

            return new Content(
                htmlString: $rendered['html'],
                text: $rendered['text'],
            );
        }

        // Fallback to view if template not found
        return new Content(
            view: 'mail.note-shared',
            with: [
                'note' => $this->note,
                'sender' => $this->sender,
                'recipient' => $this->recipient,
                'accessLevel' => $this->accessLevel,
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
