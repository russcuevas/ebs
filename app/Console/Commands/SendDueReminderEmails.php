<?php

namespace App\Console\Commands;

use App\Mail\DueReminderMail;
use App\Mail\OverdueNoticeMail;
use App\Models\BorrowingTransaction;
use App\Models\EmailNotificationLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDueReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ebs:send-due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 1-day before due reminders and overdue notices to students';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting EBS email notification processor...');

        // 1. One day before due reminder:
        // Identify transactions whose due date is tomorrow (approx 24h away) and status is ongoing
        $startWindow = now();
        $endWindow = now()->addHours(36);

        $upcomingTransactions = BorrowingTransaction::with(['student', 'items'])
            ->where('status', 'ongoing')
            ->whereBetween('due_date_time', [$startWindow, $endWindow])
            ->get();

        $remindersSent = 0;
        foreach ($upcomingTransactions as $trans) {
            if (!$trans->student || !$trans->student->email) {
                continue;
            }

            // Check if already notified
            $alreadyNotified = EmailNotificationLog::where('transaction_id', $trans->id)
                ->where('type', 'due_reminder_1_day')
                ->exists();

            if (!$alreadyNotified) {
                try {
                    Mail::to($trans->student->email)->send(new DueReminderMail($trans));

                    EmailNotificationLog::create([
                        'transaction_id' => $trans->id,
                        'type' => 'due_reminder_1_day',
                        'recipient_email' => $trans->student->email,
                        'sent_at' => now(),
                    ]);

                    $remindersSent++;
                    $this->line("Sent 1-day reminder to {$trans->student->email} for Ref #{$trans->reference_no}");
                } catch (\Exception $e) {
                    Log::error("Failed to send 1-day reminder for Ref #{$trans->reference_no}: " . $e->getMessage());
                    $this->error("Error sending reminder to {$trans->student->email}: " . $e->getMessage());
                }
            }
        }

        // 2. Overdue notifications & penalty updates
        $overdueTransactions = BorrowingTransaction::with(['student', 'items'])
            ->whereIn('status', ['ongoing', 'overdue'])
            ->where('due_date_time', '<', now())
            ->get();

        $overdueNoticesSent = 0;
        foreach ($overdueTransactions as $trans) {
            // Update penalty calculation
            $trans->calculatePenalty();

            if (!$trans->student || !$trans->student->email) {
                continue;
            }

            // Check if overdue notice was sent today
            $alreadyNotifiedToday = EmailNotificationLog::where('transaction_id', $trans->id)
                ->where('type', 'overdue_notice')
                ->whereDate('sent_at', Carbon::today())
                ->exists();

            if (!$alreadyNotifiedToday) {
                try {
                    Mail::to($trans->student->email)->send(new OverdueNoticeMail($trans));

                    EmailNotificationLog::create([
                        'transaction_id' => $trans->id,
                        'type' => 'overdue_notice',
                        'recipient_email' => $trans->student->email,
                        'sent_at' => now(),
                    ]);

                    $overdueNoticesSent++;
                    $this->line("Sent overdue notice to {$trans->student->email} for Ref #{$trans->reference_no}");
                } catch (\Exception $e) {
                    Log::error("Failed to send overdue notice for Ref #{$trans->reference_no}: " . $e->getMessage());
                    $this->error("Error sending overdue notice to {$trans->student->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Completed. Reminders sent: {$remindersSent}, Overdue notices sent: {$overdueNoticesSent}");
        return Command::SUCCESS;
    }
}
