<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PrayerAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $prayers;

    public function __construct(
        Collection $prayers,
        public User $leader
    ) {
        $this->prayers = $prayers;
    }

    public function envelope(): Envelope
    {
        $count = $this->prayers->count();
        $subject = $count === 1
            ? 'Prayer Request Assigned to You — Chrisco Upper Room'
            : "{$count} Prayer Requests Assigned to You — Chrisco Upper Room";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.prayer-assigned');
    }
}
