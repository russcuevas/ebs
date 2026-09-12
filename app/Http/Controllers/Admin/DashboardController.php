<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Refresh running penalties
        $ongoingTransactions = BorrowingTransaction::whereIn('status', ['ongoing', 'overdue'])->get();
        foreach ($ongoingTransactions as $trans) {
            $trans->calculatePenalty();
        }

        $totalBorrowings = BorrowingTransaction::count();
        $activeBorrowings = BorrowingTransaction::where('status', 'ongoing')->count();
        $overdueBorrowings = BorrowingTransaction::where('status', 'overdue')->count();
        $returnedBorrowings = BorrowingTransaction::where('status', 'returned')->count();

        $totalStaff = User::where('role', 'staff')->count();
        $totalStudents = User::where('role', 'student')->count();

        $unpaidPenalties = BorrowingTransaction::where('penalty_status', 'unpaid')->sum('total_penalty');
        $collectedPenalties = BorrowingTransaction::where('penalty_status', 'paid')->sum('total_penalty');

        $recentTransactions = BorrowingTransaction::with(['student', 'staff', 'items'])
            ->latest()
            ->take(50)
            ->get();

        return view('admin.dashboard', compact(
            'totalBorrowings',
            'activeBorrowings',
            'overdueBorrowings',
            'returnedBorrowings',
            'totalStaff',
            'totalStudents',
            'unpaidPenalties',
            'collectedPenalties',
            'recentTransactions'
        ));
    }
}
