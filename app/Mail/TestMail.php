<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $sentAt) {}

    public function build(): static
    {
        $logoPath = public_path('images/logo-email.png');
        $hasLogo  = file_exists($logoPath);

        $mail = $this->subject('Email System Test — Chrisco Upperroom Fellowship')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.test')
            ->with([
                'sentAt'  => $this->sentAt,
                'logoSrc' => $hasLogo ? 'cid:chrisco-logo' : null,
            ]);

        if ($hasLogo) {
            $mail->withSymfonyMessage(function (\Symfony\Component\Mime\Email $msg) use ($logoPath) {
                $part = DataPart::fromPath($logoPath, 'chrisco-logo', 'image/png');
                $msg->addPart($part->asInline());
            });
        }

        return $mail;
    }
}
