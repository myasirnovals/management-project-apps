<?php

namespace App\Console\Commands;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa tenggat waktu issue dan kirim pengingat otomatis ke assignee via WhatsApp dan Email';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService): int
    {
        $this->info('Memulai pengecekan tenggat waktu tugas & issue...');

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $activeIssues = Issue::with(['assignee', 'project'])
            ->where('status', '!=', IssueStatus::DONE)
            ->whereNotNull('due_date')
            ->whereNotNull('assignee_id')
            ->get();

        if ($activeIssues->isEmpty()) {
            $this->info('Tidak ada issue aktif dengan tenggat waktu yang perlu diingatkan.');
            return Command::SUCCESS;
        }

        $upcomingCount = 0;
        $overdueCount = 0;

        foreach ($activeIssues as $issue) {
            $assignee = $issue->assignee;
            if (!$assignee) {
                continue;
            }

            $dueDate = Carbon::parse($issue->due_date)->startOfDay();

            // 1. Overdue issue (tenggat waktu sebelum hari ini)
            if ($dueDate->lt($today)) {
                $overdueCount++;
                $this->processReminder($whatsAppService, $assignee, $issue, 'overdue');
            }
            // 2. Upcoming issue (tenggat waktu hari ini atau besok)
            elseif ($dueDate->lte($tomorrow)) {
                $upcomingCount++;
                $this->processReminder($whatsAppService, $assignee, $issue, 'reminder');
            }
        }

        $summary = "Pengecekan selesai. {$upcomingCount} issue mendekati tenggat waktu, {$overdueCount} issue terlambat (overdue).";
        $this->info($summary);
        Log::info("SendDueDateReminders: {$summary}");

        return Command::SUCCESS;
    }

    /**
     * Dispatch reminder message via WhatsApp Gateway and Email.
     */
    protected function processReminder(WhatsAppService $whatsAppService, $assignee, Issue $issue, string $type): void
    {
        $message = $whatsAppService->buildMessage($assignee, $issue, $type);

        // 1. Send via WhatsApp Gateway if phone and gateway configured
        if ($assignee->hasWhatsApp()) {
            $result = $whatsAppService->sendViaGateway($assignee->phone, $message);
            if ($result['success']) {
                $this->line("  [WA SENT] {$assignee->name} ({$issue->issue_key}) - {$type}");
            } else {
                $this->line("  [WA SKIP/FAIL] {$assignee->name} ({$issue->issue_key}) - {$result['message']}");
            }
        }

        // 2. Email log / dispatch
        $subject = match ($type) {
            'overdue' => "[Perhatian] Tugas {$issue->issue_key} Terlewat Tenggat Waktu",
            default => "[Pengingat] Tenggat Waktu Tugas {$issue->issue_key}",
        };

        try {
            Mail::raw($message, function ($mail) use ($assignee, $subject) {
                $mail->to($assignee->email)
                    ->subject($subject);
            });
            $this->line("  [EMAIL SENT] {$assignee->email} ({$issue->issue_key})");
        } catch (\Throwable $e) {
            $this->line("  [EMAIL FAILED] {$assignee->email}: " . $e->getMessage());
        }
    }
}
