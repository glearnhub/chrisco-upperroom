<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Monthly Attendance Report — {$this->monthName}",
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    config('mail.from.address'),
                    config('mail.from.name')
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance-report-email',
            with: [
                'monthName'      => $this->monthName,
                'inactiveCount'  => $this->inactiveCount,
                'irregularCount' => $this->irregularCount,
                'generatedAt'    => $this->generatedAt,
            ],
        );
    }

    public function attachments(): array
    {
        $filename = 'Attendance_Report_' . str_replace(' ', '_', $this->monthName) . '.pdf';

        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->pdfPath)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
