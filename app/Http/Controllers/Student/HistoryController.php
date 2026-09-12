<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        $student->refreshOngoingPenalties();

        $query = $student->borrowings()->with(['items', 'staff', 'returnedByStaff'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrowings = $query->get();

        return view('students.history', compact('borrowings'));
    }

    public function show(BorrowingTransaction $transaction)
    {
        if ($transaction->student_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $transaction->load(['items', 'staff', 'returnedByStaff']);
        $transaction->calculatePenalty();

        return view('students.show', compact('transaction'));
    }
}
