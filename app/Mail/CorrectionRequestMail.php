<?php

namespace App\Mail;

use App\Models\CorrectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorrectionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CorrectionRequest $correction) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Profile Correction Request — ' . $this->correction->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.correction-request',
        );
    }
}
