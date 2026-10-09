<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $temporaryPassword,
        public string $loginUrl,
    ) {}

    public function build(): static
    {
        $logoPath = public_path('images/logo-email.png');
        $hasLogo  = file_exists($logoPath);

        $mail = $this->subject('Your Admin Account Password Has Been Reset')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.admin-password-reset')
            ->with([
                'user'              => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl'          => $this->loginUrl,
                'logoSrc'           => $hasLogo ? 'cid:chrisco-logo' : null,
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
