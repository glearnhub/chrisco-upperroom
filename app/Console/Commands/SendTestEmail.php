<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendTestEmail extends Command
{
    protected $signature   = 'mail:test {recipient : The email address to send the test to}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle(): int
    {
        $recipient = $this->argument('recipient');

        $v = Validator::make(['email' => $recipient], ['email' => 'required|email:rfc,dns']);
        if ($v->fails()) {
            $this->error("Invalid email address: {$recipient}");
            return self::FAILURE;
        }

        $sentAt = now()->format('D, d M Y H:i:s T');

        $this->info("Sending test email to {$recipient} ...");
        $this->line('  From:    ' . config('mail.from.address'));
        $this->line('  Name:    ' . config('mail.from.name'));
        $this->line('  SMTP:    ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port'));

        try {
            Mail::to($recipient)->send(new TestMail($sentAt));
            $this->newLine();
            $this->info('✓ Test email sent successfully.');
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('✗ Failed to send test email: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
