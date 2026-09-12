<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index()
    {
        // Refresh penalties
        $ongoing = BorrowingTransaction::whereIn('status', ['ongoing', 'overdue'])->get();
        foreach ($ongoing as $trans) {
            $trans->calculatePenalty();
        }

        $penalties = BorrowingTransaction::with(['student', 'items'])
            ->where('total_penalty', '>', 0)
            ->latest()
            ->get();

        $totalCollected = BorrowingTransaction::where('penalty_status', 'paid')->sum('total_penalty');
        $totalUnpaid = BorrowingTransaction::where('penalty_status', 'unpaid')->sum('total_penalty');

        return view('admin.penalties.index', compact('penalties', 'totalCollected', 'totalUnpaid'));
    }

    public function settle(Request $request, BorrowingTransaction $transaction)
    {
        $transaction->update([
            'penalty_status' => 'paid',
            'penalty_paid_at' => now(),
        ]);

        return back()->with('success', "Penalty of ₱{$transaction->total_penalty} for Ref #{$transaction->reference_no} has been recorded as paid.");
    }

    public function waive(Request $request, BorrowingTransaction $transaction)
    {
        $transaction->update([
            'penalty_status' => 'waived',
            'penalty_paid_at' => now(),
        ]);

        return back()->with('success', "Penalty for Ref #{$transaction->reference_no} has been waived.");
    }
}
