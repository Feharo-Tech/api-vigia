<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Api;

class ApiFailureNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $api;
    public $reason;
    public $errorCount;
    public $lastResponseTime;
    public $lastStatusCode;

    /**
     * Create a new message instance.
     */
    public function __construct(Api $api, $reason, $errorCount, $lastResponseTime, $lastStatusCode)
    {
        $this->api = $api;
        $this->reason = $reason;
        $this->errorCount = $errorCount;
        $this->lastResponseTime = $lastResponseTime;
        $this->lastStatusCode = $lastStatusCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Problema Detectado na API: ' . $this->api->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.api_failure_notification',
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
