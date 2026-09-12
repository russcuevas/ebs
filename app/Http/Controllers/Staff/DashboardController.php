<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Refresh running penalties
        $ongoing = BorrowingTransaction::whereIn('status', ['ongoing', 'overdue'])->get();
        foreach ($ongoing as $trans) {
            $trans->calculatePenalty();
        }

        $activeBorrowings = BorrowingTransaction::where('status', 'ongoing')->count();
        $overdueBorrowings = BorrowingTransaction::where('status', 'overdue')->count();
        $returnedBorrowings = BorrowingTransaction::where('status', 'returned')->count();
        $totalTransactions = BorrowingTransaction::count();

        $query = BorrowingTransaction::with(['student', 'staff', 'items', 'returnedByStaff'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        return view('staff.dashboard', compact(
            'activeBorrowings',
            'overdueBorrowings',
            'returnedBorrowings',
            'totalTransactions',
            'transactions'
        ));
    }
}
