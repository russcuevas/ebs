<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowingTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students with stats & transaction counts.
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->withCount([
                'borrowings as total_borrowings_count',
                'borrowings as active_borrowings_count' => function ($query) {
                    $query->where('status', 'ongoing');
                },
                'borrowings as overdue_borrowings_count' => function ($query) {
                    $query->where('status', 'overdue');
                },
            ])
            ->with(['borrowings' => function ($q) {
                $q->where('penalty_status', 'unpaid');
            }])
            ->latest()
            ->get();

        // Calculate summary metrics
        $totalStudents = $students->count();
        $verifiedStudents = $students->whereNotNull('email_verified_at')->count();
        $unverifiedStudents = $students->whereNull('email_verified_at')->count();
        $activeStudents = $students->where('status', 'active')->count();
        $blockedStudents = $students->where('status', 'blocked')->count();

        return view('admin.students.index', compact(
            'students',
            'totalStudents',
            'verifiedStudents',
            'unverifiedStudents',
            'activeStudents',
            'blockedStudents'
        ));
    }

    /**
     * Display student profile, QR Code & complete borrowing history.
     */
    public function show(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')->with('error', 'The requested user is not a student.');
        }

        // Refresh running penalties
        $student->refreshOngoingPenalties();

        $student->load(['borrowings' => function ($query) {
            $query->with(['items', 'staff'])->latest();
        }]);

        $unpaidPenalties = $student->totalUnpaidPenalties();
        $qrSvg = $student->getQrCodeSvg();

        return view('admin.students.show', compact('student', 'unpaidPenalties', 'qrSvg'));
    }

    /**
     * Dedicated printable student QR card page (opens in new tab).
     */
    public function printCard(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')->with('error', 'The requested user is not a student.');
        }

        $qrSvg = $student->getQrCodeSvg();

        return view('admin.students.print', compact('student', 'qrSvg'));
    }

    /**
     * Show form for editing student details.
     */
    public function edit(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')->with('error', 'The requested user is not a student.');
        }

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update student details.
     */
    public function update(Request $request, User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')->with('error', 'The requested user is not a student.');
        }

        $rawEmail = trim((string) $request->input('email', ''));
        if (!empty($rawEmail)) {
            $rawEmail = preg_replace('/(@ub\.edu\.ph)+$/i', '', $rawEmail);
            if (!str_contains($rawEmail, '@')) {
                $rawEmail .= '@ub.edu.ph';
            }
            $request->merge(['email' => strtolower($rawEmail)]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($student->id)],
            'department' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($student->id),
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@ub.edu.ph')) {
                        $fail('The email address must end with @ub.edu.ph (Official UB Student Email).');
                    }
                },
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,blocked'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        $request->validate($rules, [
            'name.required' => 'Please provide the student full name.',
            'student_id.required' => 'Student ID is required.',
            'student_id.unique' => 'This Student ID is already in use.',
            'department.required' => 'Please specify the student college/department.',
            'email.required' => 'Official UB email is required.',
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $data = [
            'name' => $request->name,
            'student_id' => $request->student_id,
            'department' => $request->department,
            'email' => strtolower($request->email),
            'phone_number' => $request->phone_number,
            'status' => $request->status,
        ];

        if ($request->has('is_verified')) {
            $data['email_verified_at'] = $request->boolean('is_verified') ? ($student->email_verified_at ?? now()) : null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', "Student {$student->name} ({$student->student_id}) has been updated successfully.");
    }

    /**
     * Toggle active/blocked status for student.
     */
    public function toggleStatus(User $student)
    {
        if ($student->role !== 'student') {
            return back()->with('error', 'Action allowed only on student accounts.');
        }

        $newStatus = $student->status === 'active' ? 'blocked' : 'active';
        $student->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'activated' : 'blocked';
        return back()->with('success', "Student account for {$student->name} is now {$statusLabel}.");
    }

    /**
     * Manually mark email as verified.
     */
    public function verifyEmail(User $student)
    {
        if ($student->role !== 'student') {
            return back()->with('error', 'Action allowed only on student accounts.');
        }

        $student->update(['email_verified_at' => now()]);

        return back()->with('success', "Student {$student->name}'s email has been manually verified.");
    }

    /**
     * Delete student account if no active borrowings.
     */
    public function destroy(User $student)
    {
        if ($student->role !== 'student') {
            return back()->with('error', 'Action allowed only on student accounts.');
        }

        $activeCount = $student->borrowings()->whereIn('status', ['ongoing', 'overdue'])->count();
        if ($activeCount > 0) {
            return back()->with('error', "Cannot delete student {$student->name} because they have {$activeCount} active/overdue borrowing transactions.");
        }

        $name = $student->name;
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', "Student record for {$name} has been deleted.");
    }
}
