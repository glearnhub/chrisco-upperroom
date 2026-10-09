<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

class AdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $resetUrl
    ) {}

    public function build(): static
    {
        $logoPath = public_path('images/logo-email.png');
        $hasLogo  = file_exists($logoPath);

        $mail = $this->subject('Welcome to Chrisco Upper Room Admin Panel')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.admin-welcome')
            ->with([
                'user'     => $this->user,
                'resetUrl' => $this->resetUrl,
                'logoSrc'  => $hasLogo ? 'cid:chrisco-logo' : null,
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
