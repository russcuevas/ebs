<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowedItem;
use App\Models\BorrowingTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BorrowingController extends Controller
{
    public function create(Request $request)
    {
        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = User::where('id', $request->student_id)
                ->where('role', 'student')
                ->first();

            if ($selectedStudent) {
                $selectedStudent->refreshOngoingPenalties();
            }
        }

        return view('staff.borrowings.create', compact('selectedStudent'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'time_date_to_borrow' => ['required', 'date'],
            'time_date_to_returned' => ['required', 'date', 'after:time_date_to_borrow'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.location' => ['required', 'string', 'max:255'],
            'proof_of_borrowing' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'staff_notes' => ['nullable', 'string'],
        ], [
            'student_id.required' => 'Please scan or select a student.',
            'time_date_to_borrow.required' => 'Borrow date and time is required.',
            'time_date_to_returned.required' => 'Return date and time is required.',
            'time_date_to_returned.after' => 'Return time must be after the borrowing time.',
            'items.required' => 'Add at least one item to the list.',
            'items.*.name.required' => 'Item name is required.',
            'items.*.location.required' => 'Item location is required.',
            'proof_of_borrowing.image' => 'Proof must be a valid image file (JPEG, PNG, WEBP).',
            'proof_of_borrowing.max' => 'Image proof size must not exceed 5MB.',
        ]);

        $student = User::findOrFail($request->student_id);

        // Security check: Verify student is not blocked by overdue or unpaid penalties
        $student->refreshOngoingPenalties();
        if ($student->hasOverdueOrUnpaidPenalties()) {
            return back()->withInput()->withErrors([
                'student_id' => 'BORROWING RESTRICTED: The student currently has overdue equipment or unpaid penalties (₱' . number_format($student->totalUnpaidPenalties(), 2) . '). Overdue items must be returned and penalties settled first.',
            ]);
        }

        // Handle direct upload to public/uploads/proofs/ without storage:link
        $proofPath = null;
        if ($request->hasFile('proof_of_borrowing')) {
            $file = $request->file('proof_of_borrowing');
            $filename = 'proof_borrow_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/proofs');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $proofPath = 'uploads/proofs/' . $filename;
        }

        $referenceNo = 'EBS-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $transaction = BorrowingTransaction::create([
            'reference_no' => $referenceNo,
            'student_id' => $student->id,
            'staff_id' => Auth::id(),
            'borrow_date_time' => Carbon::parse($request->time_date_to_borrow),
            'due_date_time' => Carbon::parse($request->time_date_to_returned),
            'proof_of_borrowing' => $proofPath,
            'status' => 'ongoing',
            'penalty_per_day' => 5.00,
            'penalty_days' => 0,
            'total_penalty' => 0.00,
            'penalty_status' => 'none',
            'staff_notes' => $request->staff_notes,
        ]);

        foreach ($request->items as $itemData) {
            if (!empty($itemData['name']) && !empty($itemData['location'])) {
                BorrowedItem::create([
                    'transaction_id' => $transaction->id,
                    'item_name' => $itemData['name'],
                    'item_location' => $itemData['location'],
                    'status' => 'ongoing',
                ]);
            }
        }

        return redirect()->route('staff.dashboard')->with('success', "Borrowing recorded successfully for {$student->name}! Ref No: {$referenceNo}");
    }

    public function show(BorrowingTransaction $transaction)
    {
        $transaction->load(['student', 'staff', 'items', 'returnedByStaff']);
        $transaction->calculatePenalty();

        return view('staff.borrowings.show', compact('transaction'));
    }
}
