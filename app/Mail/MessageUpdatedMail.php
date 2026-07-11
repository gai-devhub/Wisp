<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $messageModel;
    public $changes;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $messageModel, array $changes)
    {
        $this->user = $user;
        $this->messageModel = $messageModel;
        $this->changes = $changes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'WISP Message Updated Alert',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.message-updated',
        );
    }
}
