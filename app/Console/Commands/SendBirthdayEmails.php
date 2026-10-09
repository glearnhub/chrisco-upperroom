<?php

namespace App\Console\Commands;

use App\Mail\BirthdayMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBirthdayEmails extends Command
{
    protected $signature   = 'birthday:send {--test= : Send a test birthday email to this email address or name}';
    protected $description = 'Send birthday emails to members whose birthday is today';

    public function handle(): void
    {
        if ($this->option('test')) {
            $this->sendTest($this->option('test'));
            return;
        }

        $today = now();

        $members = User::whereNotNull('email')
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth',   $today->day)
            ->get();

        $sent = 0;

        foreach ($members as $member) {
            try {
                Mail::to($member->email)->send(new BirthdayMail($member));
                $sent++;
                $this->info("Sent to {$member->full_name} <{$member->email}>");
            } catch (\Throwable $e) {
                $this->error("Failed for {$member->full_name}: {$e->getMessage()}");
            }
        }

        $this->info("Birthday emails sent: {$sent}");
    }

    private function sendTest(string $search): void
    {
        // Find member by email or name
        $member = User::where('email', $search)
            ->orWhere('name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->first();

        if (! $member) {
            $this->error("No member found matching: {$search}");
            return;
        }

        $this->info("Sending test birthday email to {$member->full_name} <{$member->email}>...");

        try {
            Mail::to($member->email)->send(new BirthdayMail($member));
            $this->info("✓ Test email sent successfully to {$member->email}");
        } catch (\Throwable $e) {
            $this->error("Failed: " . $e->getMessage());
        }
    }
}
