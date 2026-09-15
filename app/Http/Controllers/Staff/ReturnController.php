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

        if ($transaction->status === 'returned' && $transaction->isFullyReturned()) {
            return redirect()->route('staff.borrowings.show', $transaction)
                ->with('info', "All equipment items for Ref #{$transaction->reference_no} have already been fully returned.");
        }

        $penaltyInfo = $transaction->calculatePenalty();

        return view('staff.borrowings.return', compact('transaction', 'penaltyInfo'));
    }

    public function processReturn(Request $request, BorrowingTransaction $transaction)
    {
        $request->validate([
            'returned_item_ids' => ['required', 'array', 'min:1'],
            'returned_item_ids.*' => ['exists:borrowed_items,id'],
            'proof_of_return' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'staff_notes' => ['nullable', 'string'],
            'settle_penalty' => ['nullable', 'boolean'],
        ], [
            'returned_item_ids.required' => 'Please select at least one item being returned.',
            'returned_item_ids.min' => 'Please select at least one item being returned.',
            'proof_of_return.image' => 'The proof must be a valid image file (JPEG, PNG, WEBP).',
            'proof_of_return.max' => 'Image proof size must not exceed 5MB.',
        ]);

        $selectedItemIds = $request->input('returned_item_ids', []);

        // Only mark items belonging to this transaction as returned
        $transaction->items()
            ->whereIn('id', $selectedItemIds)
            ->update(['status' => 'returned']);

        // Upload proof to public/uploads/returns/
        if ($request->hasFile('proof_of_return')) {
            $file = $request->file('proof_of_return');
            $filename = 'proof_return_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/returns');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $transaction->proof_of_return = 'uploads/returns/' . $filename;
        }

        $now = now();
        $transaction->returned_by_staff_id = Auth::id();

        // Refresh transaction relations to check completion status
        $transaction->load('items');
        $totalItems = $transaction->totalItemsCount();
        $returnedItems = $transaction->returnedItemsCount();
        $isAllReturned = ($returnedItems >= $totalItems);

        $justReturnedNames = $transaction->items()
            ->whereIn('id', $selectedItemIds)
            ->pluck('item_name')
            ->implode(', ');

        if ($isAllReturned) {
            // ALL items returned (e.g. 3/3): tag as returned & freeze final penalty
            $transaction->status = 'returned';
            $transaction->return_date_time = $now;

            if ($request->filled('staff_notes')) {
                $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') .
                    "[Full Return - " . $now->format('M d, Y h:i A') . "]: Returned items ({$justReturnedNames}). " . $request->staff_notes;
            } else {
                $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') .
                    "[Full Return - " . $now->format('M d, Y h:i A') . "]: All {$totalItems}/{$totalItems} items completed.";
            }

            // Calculate final penalty based on completion time
            $penalty = $transaction->calculatePenalty($now);

            if ($penalty['amount'] > 0) {
                if ($transaction->penalty_status === 'paid') {
                    // Already paid previously - preserve PAID status!
                } elseif ($request->boolean('settle_penalty')) {
                    $transaction->penalty_status = 'paid';
                    $transaction->penalty_paid_at = $now;
                } else {
                    $transaction->penalty_status = 'unpaid';
                }
            } else {
                if ($transaction->penalty_status !== 'paid') {
                    $transaction->penalty_status = 'none';
                }
            }

            $transaction->save();

            $message = "Complete Return! All {$totalItems}/{$totalItems} items have been safely returned. Ref #{$transaction->reference_no} is now marked as RETURNED.";
            if ($penalty['amount'] > 0) {
                if ($transaction->penalty_status === 'paid') {
                    $message .= " Penalty payment of ₱" . number_format($penalty['amount'], 2) . " has been settled as PAID.";
                } else {
                    $message .= " Outstanding penalty: ₱" . number_format($penalty['amount'], 2) . ". Student must settle penalty to unblock borrowing privileges.";
                }
            }

            return redirect()->route('staff.dashboard')->with('success', $message);
        } else {
            // PARTIAL Return (e.g. 1/3 or 2/3): remain ongoing/overdue, penalties continue counting
            $isOverdue = $now->greaterThan($transaction->due_date_time);
            $transaction->status = $isOverdue ? 'overdue' : 'ongoing';
            $transaction->return_date_time = null; // Still active, not yet fully returned

            if ($request->filled('staff_notes')) {
                $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') .
                    "[Partial Return - " . $now->format('M d, Y h:i A') . "]: Returned ({$justReturnedNames}) [{$returnedItems}/{$totalItems} items returned]. " . $request->staff_notes;
            } else {
                $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') .
                    "[Partial Return - " . $now->format('M d, Y h:i A') . "]: Returned ({$justReturnedNames}) [{$returnedItems}/{$totalItems} items returned].";
            }

            // Calculate running penalty (keeps running until all items are returned)
            $penalty = $transaction->calculatePenalty();

            if ($request->boolean('settle_penalty') && $penalty['amount'] > 0) {
                $transaction->penalty_status = 'paid';
                $transaction->penalty_paid_at = $now;
            } elseif ($transaction->penalty_status !== 'paid' && $penalty['amount'] > 0) {
                $transaction->penalty_status = 'unpaid';
            }

            $transaction->save();

            $pending = $totalItems - $returnedItems;
            $message = "Partial Return Recorded: {$returnedItems}/{$totalItems} items returned ({$pending} item(s) still pending). Status remains active and penalties continue counting until all items are returned.";

            return redirect()->route('staff.borrowings.return', $transaction)->with('warning', $message);
        }
    }

    public function settlePenalty(Request $request, BorrowingTransaction $transaction)
    {
        $request->validate([
            'amount_received' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $now = now();
        $penaltyAmount = (float) $transaction->total_penalty;
        if ($penaltyAmount <= 0) {
            $calc = $transaction->calculatePenalty();
            $penaltyAmount = (float) $calc['amount'];
            $transaction->penalty_days = $calc['days'];
            $transaction->total_penalty = $penaltyAmount;
        }

        $transaction->penalty_status = 'paid';
        $transaction->penalty_paid_at = $now;

        $staffName = Auth::user()->name ?? 'Staff';
        $logText = "[Penalty Paid - " . $now->format('M d, Y h:i A') . " by {$staffName}]: ₱" . number_format($penaltyAmount, 2);
        
        if ($request->filled('remarks')) {
            $logText .= " | Note/Receipt: " . $request->remarks;
        }

        if ($request->filled('amount_received') && (float) $request->amount_received >= $penaltyAmount) {
            $change = (float) $request->amount_received - $penaltyAmount;
            $logText .= " (Cash Tendered: ₱" . number_format($request->amount_received, 2) . ", Change: ₱" . number_format($change, 2) . ")";
        }

        $transaction->staff_notes = ($transaction->staff_notes ? $transaction->staff_notes . "\n" : '') . $logText;
        $transaction->save();

        return back()->with('success', "Penalty payment of ₱" . number_format($penaltyAmount, 2) . " for Ref #{$transaction->reference_no} has been successfully collected and recorded as PAID!");
    }

    public function settleStudentPenalties(Request $request, \App\Models\User $student)
    {
        $request->validate([
            'amount_received' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $student->refreshOngoingPenalties();

        $unpaidTransactions = $student->borrowings()
            ->where('penalty_status', 'unpaid')
            ->where('total_penalty', '>', 0)
            ->get();

        if ($unpaidTransactions->isEmpty()) {
            return back()->with('info', "{$student->name} has no outstanding unpaid penalties.");
        }

        $now = now();
        $totalSettled = 0;
        $staffName = Auth::user()->name ?? 'Staff';

        foreach ($unpaidTransactions as $trans) {
            $amount = (float) $trans->total_penalty;
            $trans->penalty_status = 'paid';
            $trans->penalty_paid_at = $now;

            $log = "[Penalty Paid - " . $now->format('M d, Y h:i A') . " by {$staffName}]: ₱" . number_format($amount, 2);
            if ($request->filled('remarks')) {
                $log .= " | Note/Receipt: " . $request->remarks;
            }
            $trans->staff_notes = ($trans->staff_notes ? $trans->staff_notes . "\n" : '') . $log;
            $trans->save();

            $totalSettled += $amount;
        }

        return back()->with('success', "Successfully collected total penalties of ₱" . number_format($totalSettled, 2) . " from {$student->name}! Outstanding penalties have been settled.");
    }
}
