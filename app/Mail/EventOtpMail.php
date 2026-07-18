<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $eventTitle
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Event Registration Code — ' . $this->eventTitle);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.event-otp');
    }
}
