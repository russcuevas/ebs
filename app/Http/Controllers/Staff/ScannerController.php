<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('staff.scanner.index');
    }

    public function lookupStudent(Request $request)
    {
        $code = trim($request->input('code', ''));

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide or scan a student QR code or Student ID.',
            ], 422);
        }

        // Find student by qr_code_token, student_id, or email
        $student = User::where('role', 'student')
            ->where(function ($q) use ($code) {
                $q->where('qr_code_token', $code)
                  ->orWhere('student_id', $code)
                  ->orWhere('email', $code);
            })
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "Student not found with code: '{$code}'. Please verify the student is registered and verified at @ub.edu.ph.",
            ], 404);
        }

        // Refresh running penalties
        $student->refreshOngoingPenalties();

        $isBlocked = $student->hasOverdueOrUnpaidPenalties();
        $unpaidPenalties = $student->totalUnpaidPenalties();

        // Get student's current active or overdue borrowings
        $activeBorrowings = $student->borrowings()
            ->with('items')
            ->whereIn('status', ['ongoing', 'overdue'])
            ->latest()
            ->get();

        $blockReasons = [];
        if ($isBlocked) {
            $hasOverdue = $activeBorrowings->where('status', 'overdue')->count() > 0;
            if ($hasOverdue) {
                $blockReasons[] = 'Currently has OVERDUE equipment that has not been returned.';
            }
            if ($unpaidPenalties > 0) {
                $blockReasons[] = 'Has an outstanding unpaid penalty of ₱' . number_format($unpaidPenalties, 2) . '.';
            }
        }

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'student_id' => $student->student_id,
                'department' => $student->department,
                'phone_number' => $student->phone_number,
                'avatar_url' => $student->avatar_url,
                'is_verified' => (bool) $student->email_verified_at,
            ],
            'is_blocked' => $isBlocked,
            'unpaid_penalties' => $unpaidPenalties,
            'block_reasons' => $blockReasons,
            'active_borrowings' => $activeBorrowings,
        ]);
    }
}
