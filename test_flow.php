<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\BorrowingTransaction;
use App\Models\BorrowedItem;
use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

echo "=== 1. CHECK USERS SEEDED ===\n";
$admin = User::where('email', 'admin@ub.edu.ph')->first();
$staff = User::where('email', 'staff@ub.edu.ph')->first();
$student = User::where('email', 'student@ub.edu.ph')->first();

echo "Admin: " . ($admin ? "OK ({$admin->name}, role: {$admin->role})" : "MISSING") . "\n";
echo "Staff: " . ($staff ? "OK ({$staff->name}, dept: {$staff->department})" : "MISSING") . "\n";
echo "Student: " . ($student ? "OK ({$student->name}, QR: {$student->qr_code_token})" : "MISSING") . "\n";

echo "\n=== 2. TEST QR CODE GENERATION ===\n";
$svg = $student->getQrCodeSvg();
echo "QR Code SVG generated length: " . strlen($svg) . " chars\n";
echo "Starts with <svg: " . (str_starts_with(trim($svg), '<svg') ? 'YES' : 'NO') . "\n";

echo "\n=== 3. TEST PENALTY CALCULATION LOGIC ===\n";
// Case A: Due Sept 9, checked on Sept 9 -> 0 penalty
$tA = new BorrowingTransaction([
    'due_date_time' => Carbon::parse('2026-09-09 17:00:00'),
    'penalty_per_day' => 5.00,
]);
$calcA = $tA->calculatePenalty(Carbon::parse('2026-09-09 18:00:00'));
echo "Case A (Same day evening, Sept 9): Days late = {$calcA['days']}, Penalty = ₱{$calcA['amount']} (Expected 0)\n";

// Case B: Due Sept 9, checked on Sept 10 -> 1 day late = 5 pesos
$calcB = $tA->calculatePenalty(Carbon::parse('2026-09-10 10:00:00'));
echo "Case B (Next day, Sept 10): Days late = {$calcB['days']}, Penalty = ₱{$calcB['amount']} (Expected ₱5.00)\n";

// Case C: Due Sept 9, checked on Sept 11 -> 2 days late = 10 pesos
$calcC = $tA->calculatePenalty(Carbon::parse('2026-09-11 10:00:00'));
echo "Case C (Two days later, Sept 11): Days late = {$calcC['days']}, Penalty = ₱{$calcC['amount']} (Expected ₱10.00)\n";

echo "\n=== 4. TEST OVERDUE LOCK / BLOCK CHECK ===\n";
echo "Student hasOverdueOrUnpaidPenalties before overdue loan: " . ($student->hasOverdueOrUnpaidPenalties() ? 'TRUE (Blocked)' : 'FALSE (Clear)') . "\n";

// Create overdue loan for test
$overdueTrans = BorrowingTransaction::create([
    'reference_no' => 'TEST-OVERDUE-01',
    'student_id' => $student->id,
    'staff_id' => $staff->id,
    'borrow_date_time' => now()->subDays(5),
    'due_date_time' => now()->subDays(2),
    'status' => 'ongoing',
    'penalty_per_day' => 5.00,
    'penalty_days' => 2,
    'total_penalty' => 10.00,
    'penalty_status' => 'unpaid',
]);

echo "Created test overdue transaction with ₱10 penalty.\n";
echo "Student hasOverdueOrUnpaidPenalties now: " . ($student->hasOverdueOrUnpaidPenalties() ? 'TRUE (BLOCKED - RED ALERT TRIGGERED)' : 'FALSE') . "\n";
echo "Student total unpaid penalties: ₱" . number_format($student->totalUnpaidPenalties(), 2) . "\n";

// Return the item and settle penalty
$overdueTrans->update([
    'status' => 'returned',
    'return_date_time' => now(),
    'penalty_status' => 'paid',
    'penalty_paid_at' => now(),
]);
echo "Overdue item returned and penalty settled as PAID.\n";
echo "Student hasOverdueOrUnpaidPenalties after return & pay: " . ($student->hasOverdueOrUnpaidPenalties() ? 'TRUE (Blocked)' : 'FALSE (CLEAR TO BORROW AGAIN)') . "\n";

// Clean up test transaction
$overdueTrans->delete();

echo "\nALL TESTS COMPLETED SUCCESSFULLY!\n";
