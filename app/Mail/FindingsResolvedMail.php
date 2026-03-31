<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FindingsResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $projectCode,
        public string $inspectionName,
        public string $reportUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "All findings resolved — {$this->inspectionName} ({$this->projectCode})");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.findings-resolved');
    }
}
