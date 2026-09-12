<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = User::where('role', 'staff')
            ->withCount('staffBorrowings')
            ->latest()
            ->get();

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'department' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Please provide the staff member full name.',
            'email.required' => 'Please enter the email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'A password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'department.required' => 'Please specify the department or office.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'role' => 'staff',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created successfully for ' . $request->name . '!');
    }

    public function edit(User $staff)
    {
        if ($staff->role !== 'staff') {
            return redirect()->route('admin.staff.index')->with('error', 'The selected user is not a staff member.');
        }

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->role !== 'staff') {
            return redirect()->route('admin.staff.index')->with('error', 'The selected user is not a staff member.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'department' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,blocked'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        $request->validate($rules, [
            'name.required' => 'Please provide the staff member full name.',
            'email.required' => 'Please enter the email address.',
            'email.unique' => 'This email address is already registered.',
            'department.required' => 'Please specify the department or office.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'department' => $request->department,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account updated successfully for ' . $staff->name . '.');
    }

    public function destroy(User $staff)
    {
        if ($staff->role !== 'staff') {
            return back()->with('error', 'This user cannot be deleted.');
        }

        $name = $staff->name;
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff account deleted successfully for ' . $name . '.');
    }
}
