<?php

namespace App\Console\Commands;

use App\Mail\MonthlyAttendanceReportMail;
use App\Models\ServiceAttendance;
use App\Models\ServiceSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendMonthlyAttendanceReport extends Command
{
    protected $signature = 'attendance:monthly-report
                            {--month= : Month number (1-12); defaults to previous month}
                            {--year=  : Year; defaults to current year (or previous year if month is January)}
                            {--recipient= : Override recipient email; defaults to admin@chriscoupperroom.org}';

    protected $description = 'Generate and email the monthly inactive/irregular attendance report';

    public function handle(): int
    {
        // Resolve month/year — default to the previous calendar month
        if ($this->option('month')) {
            $month = (int) $this->option('month');
            $year  = $this->option('year') ? (int) $this->option('year') : now()->year;
        } else {
            $ref   = now()->subMonth();
            $month = $ref->month;
            $year  = $ref->year;
        }

        if ($month < 1 || $month > 12) {
            $this->error("Invalid month: {$month}. Must be 1–12.");
            return self::FAILURE;
        }

        $recipient = $this->option('recipient') ?: 'admin@chriscoupperroom.org';

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();
        $monthName    = $startOfMonth->format('F Y');

        $this->info("Generating attendance report for {$monthName}…");

        // ── Build Sunday list ──────────────────────────────────────────
        $sundays = [];
        $cur = $startOfMonth->copy();
        while ($cur->lte($endOfMonth)) {
            if ($cur->isSunday()) { $sundays[] = $cur->toDateString(); }
            $cur->addDay();
        }
        $totalSundays = count($sundays);

        if ($totalSundays === 0) {
            $this->warn("No Sundays found for {$monthName}. Aborting.");
            return self::FAILURE;
        }

        // ── Build attendance map ───────────────────────────────────────
        $sessions = ServiceSession::whereIn('service_date', $sundays)
            ->whereIn('service_type', ['sunday_morning', 'sunday_afternoon'])
            ->orderBy('service_date')->orderBy('service_type')
            ->get();

        $sessionIds = $sessions->pluck('id')->toArray();

        $allAttendances = ServiceAttendance::whereIn('session_id', $sessionIds)
            ->get(['session_id', 'user_id']);

        $sessionDateMap = [];
        foreach ($sessions as $s) {
            $sessionDateMap[$s->id] = $s->service_date instanceof Carbon
                ? $s->service_date->toDateString() : $s->service_date;
        }

        $memberSundayMap = [];
        foreach ($allAttendances as $att) {
            $date = $sessionDateMap[$att->session_id] ?? null;
            if ($date) { $memberSundayMap[$att->user_id][$date] = true; }
        }

        // ── Classify members ──────────────────────────────────────────
        $members = User::where('role', 'member')->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'middle_name', 'last_name', 'phone', 'department', 'office']);

        $inactiveMembers  = [];
        $irregularMembers = [];

        foreach ($members as $member) {
            $attended = count($memberSundayMap[$member->id] ?? []);
            $missed   = $totalSundays - $attended;
            $member->sundays_attended = $attended;
            $member->sundays_missed   = $missed;
            $member->attended_dates   = array_keys($memberSundayMap[$member->id] ?? []);

            if ($attended >= 3) { continue; }
            if ($missed >= 3)   { $inactiveMembers[]  = $member; }
            else                { $irregularMembers[] = $member; }
        }
        usort($inactiveMembers, fn($a, $b) => $b->sundays_missed <=> $a->sundays_missed);

        $this->info(sprintf(
            'Found %d inactive and %d irregular members.',
            count($inactiveMembers),
            count($irregularMembers)
        ));

        // ── Generate PDF ───────────────────────────────────────────────
        $generatedAt = now()->format('D, d M Y H:i:s T');

        $pdf = Pdf::loadView('emails.attendance-report-pdf', [
            'monthName'       => $monthName,
            'totalSundays'    => $totalSundays,
            'inactiveMembers' => $inactiveMembers,
            'irregularMembers'=> $irregularMembers,
            'generatedAt'     => $generatedAt,
        ])->setPaper('a4', 'portrait');

        $pdfFilename = 'attendance_report_' . $startOfMonth->format('Y_m') . '.pdf';
        $pdfPath     = storage_path('app/' . $pdfFilename);

        file_put_contents($pdfPath, $pdf->output());
        $this->info("PDF saved temporarily to: {$pdfPath}");

        // ── Send email ─────────────────────────────────────────────────
        $this->info("Sending report to: {$recipient}…");

        try {
            Mail::to($recipient)->send(new MonthlyAttendanceReportMail(
                monthName:      $monthName,
                inactiveCount:  count($inactiveMembers),
                irregularCount: count($irregularMembers),
                pdfPath:        $pdfPath,
                generatedAt:    $generatedAt,
            ));

            $this->info("Report emailed successfully to {$recipient}.");
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            @unlink($pdfPath);
            return self::FAILURE;
        }

        // Remove temp PDF after sending
        @unlink($pdfPath);

        return self::SUCCESS;
    }
}
