<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ebs:test-penalty {--days=3}', function () {
    $days = (int) $this->option('days');
    $student = \App\Models\User::where('role', 'student')->first();
    $staff = \App\Models\User::where('role', 'staff')->first();

    if (!$student) {
        $this->error('No student found in database. Run php artisan db:seed first.');
        return;
    }

    $ref = 'EBS-' . date('Ymd') . '-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT);
    $borrowDate = now()->subDays($days + 1)->setTime(9, 0);
    $dueDate = now()->subDays($days)->setTime(17, 0);

    $trans = \App\Models\BorrowingTransaction::create([
        'reference_no' => $ref,
        'student_id' => $student->id,
        'staff_id' => $staff?->id,
        'borrow_date_time' => $borrowDate,
        'due_date_time' => $dueDate,
        'status' => 'ongoing',
        'penalty_per_day' => 5.00,
        'staff_notes' => 'Test overdue transaction generated to test penalty computation and partial return.',
    ]);

    \App\Models\BorrowedItem::create([
        'transaction_id' => $trans->id,
        'item_name' => 'Projector Remote Control',
        'item_location' => 'CCS AV Room',
        'status' => 'ongoing',
    ]);
    \App\Models\BorrowedItem::create([
        'transaction_id' => $trans->id,
        'item_name' => 'HDMI to USB-C Adapter',
        'item_location' => 'Lab 3',
        'status' => 'ongoing',
    ]);
    \App\Models\BorrowedItem::create([
        'transaction_id' => $trans->id,
        'item_name' => 'Wireless Presenter Clicker',
        'item_location' => 'Dean Office',
        'status' => 'ongoing',
    ]);

    $penalty = $trans->calculatePenalty();

    $this->info("Successfully created test overdue transaction!");
    $this->line("• Reference No: {$trans->reference_no}");
    $this->line("• Student: {$student->name} ({$student->student_id})");
    $this->line("• Due Date: " . $trans->due_date_time->format('M d, Y h:i A') . " ({$penalty['days']} day(s) overdue)");
    $this->line("• Penalty: ₱" . number_format($penalty['amount'], 2) . " (₱5.00/day)");
    $this->line("• Items (3): Projector Remote Control, HDMI to USB-C Adapter, Wireless Presenter Clicker");
    $this->info("You can now test it in your browser:");
    $this->line("  Process Return:   http://127.0.0.1:8000/staff/borrowings/{$trans->id}/return");
    $this->line("  Staff Dashboard:  http://127.0.0.1:8000/staff/dashboard");
    $this->line("  Student Dashboard:http://127.0.0.1:8000/student/dashboard");
})->purpose('Generate an overdue transaction with 3 items to test penalties and partial returns');

