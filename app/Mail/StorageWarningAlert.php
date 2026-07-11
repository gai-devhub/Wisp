<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StorageWarningAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $percentage;
    public $formattedSize;
    public $limit;

    /**
     * Create a new message instance.
     */
    public function __construct($percentage, $formattedSize, $limit = '2.0 TB')
    {
        $this->percentage = $percentage;
        $this->formattedSize = $formattedSize;
        $this->limit = $limit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CRITICAL ALERT: System Storage Exceeded 90% Limit',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.storage-warning-alert',
        );
    }
}
