<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudyDirectorAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $directorName,
        public string $projectCode,
        public string $projectTitle,
        public string $projectUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "You have been appointed Study Director — {$this->projectCode}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.study-director-assigned');
    }
}
