<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

class BirthdayMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $greeting;   // "Happy Birthday Deacon Gideon" — banner
    public string $salutation; // "Deacon Gideon" — Dear ...
    public string $subjectLine;

    public function __construct(public User $member)
    {
        $office    = strtolower(trim($member->office ?? ''));
        $firstName = $member->name;

        if ($office === 'presbyter') {
            $this->subjectLine = 'Happy Birthday Dad';
            $this->greeting    = 'Happy Birthday Dad';
            $this->salutation  = 'Dad';
        } elseif ($office === 'pastor') {
            $this->subjectLine = 'Happy Birthday Mum';
            $this->greeting    = 'Happy Birthday Mum';
            $this->salutation  = 'Mum';
        } else {
            $title = match ($office) {
                'elder'     => 'Elder',
                'deacon'    => 'Deacon',
                'deaconess' => 'Deaconess',
                default     => '',
            };
            $label = $title ? "{$title} {$firstName}" : $firstName;
            $this->subjectLine = "Happy Birthday {$label}";
            $this->greeting    = "Happy Birthday {$label}";
            $this->salutation  = $label;
        }
    }

    public function build(): static
    {
        $logoPath   = public_path('images/logo-email.png');
        $hasLogo    = file_exists($logoPath);

        $mail = $this->subject($this->subjectLine)
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.birthday')
            ->with([
                'greeting'   => $this->greeting,
                'salutation' => $this->salutation,
                'logoSrc'    => $hasLogo ? 'cid:chrisco-logo' : null,
            ]);

        if ($hasLogo) {
            $mail->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($logoPath) {
                $part = DataPart::fromPath($logoPath, 'chrisco-logo', 'image/png');
                $message->addPart($part->asInline());
            });
        }

        return $mail;
    }
}
