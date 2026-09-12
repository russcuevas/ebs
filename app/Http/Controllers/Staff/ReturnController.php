<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    public function showReturnForm(BorrowingTransaction $transaction)
    {
        $transaction->load(['student', 'items', 'staff']);
        $penaltyInfo = $transaction->calculatePenalty();

        return view('staff.borrowings.return', compact('transaction', 'penaltyInfo'));
    }

    public function processReturn(Request $request, BorrowingTransaction $transaction)
    {
        $request->validate([
            'proof_of_return' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'staff_notes' => ['nullable', 'string'],
            'settle_penalty' => ['nullable', 'boolean'],
        ], [
            'proof_of_return.required' => 'An image proof of the returned equipment is required.',
            'proof_of_return.image' => 'The proof must be a valid image file (JPEG, PNG, WEBP).',
            'proof_of_return.max' => 'Image proof size must not exceed 5MB.',
        ]);

        // Upload proof to public/uploads/returns/ directly without storage:link
        $proofPath = null;
        if ($request->hasFile('proof_of_return')) {
            $file = $request->file('proof_of_return');
            $filename = 'proof_return_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/returns');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $proofPath = 'uploads/returns/' . $filename;
        }

        $now = now();
        $transaction->return_date_time = $now;
        $transaction->proof_of_return = $proofPath;
        $transaction->returned_by_staff_id = Auth::id();
        $transaction->status = 'returned';

        if ($request->filled('staff_notes')) {
            $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') .
                "[Return Log - " . $now->format('M d, Y h:i A') . "]: " . $request->staff_notes;
        }

        // Calculate penalty based on actual return time
        $penalty = $transaction->calculatePenalty($now);

        if ($penalty['amount'] > 0) {
            if ($request->boolean('settle_penalty')) {
                $transaction->penalty_status = 'paid';
                $transaction->penalty_paid_at = $now;
            } else {
                $transaction->penalty_status = 'unpaid';
            }
        } else {
            $transaction->penalty_status = 'none';
        }

        $transaction->save();

        // Mark all items under transaction as returned
        $transaction->items()->update(['status' => 'returned']);

        $message = "Equipment return successfully recorded for Ref #{$transaction->reference_no}!";
        if ($penalty['amount'] > 0) {
            if ($transaction->penalty_status === 'paid') {
                $message .= " Penalty payment of ₱" . number_format($penalty['amount'], 2) . " has been recorded as paid.";
            } else {
                $message .= " There is an outstanding penalty of ₱" . number_format($penalty['amount'], 2) . ". Please settle to restore borrowing privileges.";
            }
        }

        return redirect()->route('staff.dashboard')->with('success', $message);
    }

    public function settlePenalty(Request $request, BorrowingTransaction $transaction)
    {
        $transaction->update([
            'penalty_status' => 'paid',
            'penalty_paid_at' => now(),
        ]);

        return back()->with('success', "Penalty of ₱{$transaction->total_penalty} for Ref #{$transaction->reference_no} has been settled.");
    }
}
