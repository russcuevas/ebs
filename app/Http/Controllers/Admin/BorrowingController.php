<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DueReminderMail;
use App\Mail\OverdueNoticeMail;
use App\Models\BorrowingTransaction;
use App\Models\EmailNotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        // Refresh running penalties
        $ongoing = BorrowingTransaction::whereIn('status', ['ongoing', 'overdue'])->get();
        foreach ($ongoing as $trans) {
            $trans->calculatePenalty();
        }

        $query = BorrowingTransaction::with(['student', 'staff', 'items', 'returnedByStaff'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('student_id', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('item_name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->get();

        return view('admin.borrowings.index', compact('transactions'));
    }

    public function show(BorrowingTransaction $transaction)
    {
        $transaction->load(['student', 'staff', 'returnedByStaff', 'items', 'notificationLogs']);
        $transaction->calculatePenalty();

        return view('admin.borrowings.show', compact('transaction'));
    }

    public function sendReminderEmail(BorrowingTransaction $transaction)
    {
        if (!$transaction->student || !$transaction->student->email) {
            return back()->with('error', 'The student does not have a registered email address.');
        }

        $transaction->calculatePenalty();

        try {
            if ($transaction->isOverdue()) {
                Mail::to($transaction->student->email)->send(new OverdueNoticeMail($transaction));
                $type = 'overdue_notice';
                $msg = 'Overdue Notice email has been sent successfully to the student!';
            } else {
                Mail::to($transaction->student->email)->send(new DueReminderMail($transaction));
                $type = 'due_reminder_1_day';
                $msg = 'Due Date Reminder email has been sent successfully to the student!';
            }

            EmailNotificationLog::create([
                'transaction_id' => $transaction->id,
                'type' => $type,
                'recipient_email' => $transaction->student->email,
                'sent_at' => now(),
            ]);

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
