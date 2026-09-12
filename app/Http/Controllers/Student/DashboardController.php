<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Refresh penalties
        $student->refreshOngoingPenalties();

        $qrCodeSvg = $student->getQrCodeSvg();

        $activeBorrowings = $student->borrowings()
            ->with(['items', 'staff'])
            ->whereIn('status', ['ongoing', 'overdue'])
            ->latest()
            ->get();

        $totalReturned = $student->borrowings()->where('status', 'returned')->count();
        $totalOngoing = $student->borrowings()->where('status', 'ongoing')->count();
        $totalOverdue = $student->borrowings()->where('status', 'overdue')->count();

        $totalUnpaidPenalties = $student->totalUnpaidPenalties();
        $isBlocked = $student->hasOverdueOrUnpaidPenalties();

        return view('students.dashboard', compact(
            'student',
            'qrCodeSvg',
            'activeBorrowings',
            'totalReturned',
            'totalOngoing',
            'totalOverdue',
            'totalUnpaidPenalties',
            'isBlocked'
        ));
    }
}
