<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

class MonthlyAttendanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $monthName,
        public int    $inactiveCount,
        public int    $irregularCount,
        public string $pdfPath,
        public string $generatedAt,
    ) {}

    public function build(): static
    {
        $logoPath = public_path('images/logo-email.png');
        $hasLogo  = file_exists($logoPath);

        $filename = 'Attendance_Report_' . str_replace(' ', '_', $this->monthName) . '.pdf';

        $mail = $this->subject("Monthly Attendance Report — {$this->monthName}")
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.attendance-report-email')
            ->with([
                'monthName'      => $this->monthName,
                'inactiveCount'  => $this->inactiveCount,
                'irregularCount' => $this->irregularCount,
                'generatedAt'    => $this->generatedAt,
                'logoSrc'        => $hasLogo ? 'cid:chrisco-logo' : null,
            ])
            ->attach($this->pdfPath, [
                'as'   => $filename,
                'mime' => 'application/pdf',
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
