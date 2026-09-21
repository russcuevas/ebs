@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Student Management</h2>
            <p class="text-muted small mb-0">Directory and account management of all registered University of Batangas students</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-white text-dark border px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-users text-primary"></i> Total Students: <span class="fw-bold text-primary">{{ $totalStudents }}</span>
            </span>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-ub p-3 border-start border-4 border-primary h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;" class="flex-shrink-0">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Students</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalStudents }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-ub p-3 border-start border-4 border-success h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; background: #ecfdf5; color: #059669; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;" class="flex-shrink-0">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Verified via OTP</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $verifiedStudents }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-ub p-3 border-start border-4 border-warning h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; background: #fffbeb; color: #d97706; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;" class="flex-shrink-0">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pending OTP</div>
                        <h4 class="fw-bold mb-0 text-warning">{{ $unverifiedStudents }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-ub p-3 border-start border-4 border-danger h-100">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; background: #fef2f2; color: #dc2626; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;" class="flex-shrink-0">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Blocked Accounts</div>
                        <h4 class="fw-bold mb-0 text-danger">{{ $blockedStudents }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Students Directory Card -->
    <div class="card card-ub overflow-hidden shadow-sm">
        <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 34px; height: 34px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Registered Students Directory</span>
                    <span class="text-muted small ms-2 d-none d-md-inline">({{ count($students) }} records)</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Student ID</th>
                            <th>College / Department</th>
                            <th>UB Gmail & Contact</th>
                            <th>OTP Status</th>
                            <th>Account Status</th>
                            <th>Borrowings</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @php
                                $unpaidSum = $student->borrowings->sum('total_penalty');
                            @endphp
                            <tr>
                                <!-- Student Avatar & Name -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if ($student->profile_photo_path && file_exists(public_path($student->profile_photo_path)))
                                            <img src="{{ asset($student->profile_photo_path) }}" alt="{{ $student->name }}"
                                                class="rounded-circle border shadow-sm flex-shrink-0"
                                                style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                                                style="width: 36px; height: 36px; font-size: 13.5px; background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.students.show', $student) }}" class="fw-bold text-dark text-decoration-none text-nowrap hover-maroon" style="font-size: 13.5px;">
                                                {{ $student->name }}
                                            </a>
                                            <div class="text-muted small" style="font-size: 11px;">
                                                Registered {{ $student->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Student ID Badge -->
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold font-monospace" style="font-size: 12px; letter-spacing: 0.5px;">
                                        <i class="fa-solid fa-id-card text-muted me-1"></i> {{ $student->student_id ?? 'N/A' }}
                                    </span>
                                </td>

                                <!-- Department -->
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 12px;">
                                        {{ $student->department ?? 'General' }}
                                    </span>
                                </td>

                                <!-- Email & Phone -->
                                <td class="text-nowrap">
                                    <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                        <i class="fa-regular fa-envelope text-muted me-1"></i> {{ $student->email }}
                                    </div>
                                    @if ($student->phone_number)
                                        <div class="text-muted small" style="font-size: 11.5px;">
                                            <i class="fa-solid fa-phone text-muted me-1"></i> {{ $student->phone_number }}
                                        </div>
                                    @endif
                                </td>

                                <!-- OTP Verification -->
                                <td class="text-nowrap">
                                    @if ($student->email_verified_at)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;" title="Verified on {{ $student->email_verified_at->format('M d, Y h:i A') }}">
                                            <i class="fa-solid fa-circle-check me-1"></i> Verified
                                        </span>
                                    @else
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="fa-solid fa-clock me-1"></i> Pending OTP
                                            </span>
                                            <form action="{{ route('admin.students.verify-email', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Manually verify {{ $student->name }}\'s account?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success py-0 px-1.5" style="font-size: 10.5px; border-radius: 4px;" title="Force Verify Email">
                                                    Verify
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>

                                <!-- Account Status -->
                                <td class="text-nowrap">
                                    @if ($student->status === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                            <i class="fa-solid fa-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                            <i class="fa-solid fa-ban me-1"></i> Blocked
                                        </span>
                                    @endif
                                </td>

                                <!-- Borrowings & Penalty Stats -->
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" title="Total borrowings">
                                            {{ $student->total_borrowings_count }} total
                                        </span>
                                        @if ($student->active_borrowings_count > 0)
                                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold" title="Currently holding items">
                                                {{ $student->active_borrowings_count }} active
                                            </span>
                                        @endif
                                        @if ($student->overdue_borrowings_count > 0)
                                            <span class="badge bg-danger text-white px-2 py-1 fw-bold" title="Overdue items">
                                                {{ $student->overdue_borrowings_count }} overdue
                                            </span>
                                        @endif
                                    </div>
                                    @if ($unpaidSum > 0)
                                        <div class="text-danger fw-bold mt-1" style="font-size: 11px;">
                                            <i class="fa-solid fa-receipt me-1"></i> ₱{{ number_format($unpaidSum, 2) }} unpaid
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="text-end pe-4 text-nowrap">
                                    <div class="d-inline-flex gap-1.5 align-items-center">
                                        <!-- View Profile & QR -->
                                        <a href="{{ route('admin.students.show', $student) }}"
                                            class="btn btn-sm btn-ub-primary d-inline-flex align-items-center justify-content-center shadow-sm"
                                            style="height: 32px; padding: 0 10px; font-size: 12px; border-radius: 6px;" title="View Student QR & Borrowing History">
                                            <i class="fa-solid fa-qrcode me-1"></i> View / QR
                                        </a>

                                        <!-- Print Card in New Tab -->
                                        <a href="{{ route('admin.students.print', $student) }}" target="_blank"
                                            class="btn btn-sm btn-light border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; border-radius: 6px;" title="Print Student QR Pass">
                                            <i class="fa-solid fa-print text-dark"></i>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                            class="btn btn-sm btn-light border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; border-radius: 6px;" title="Edit Student">
                                            <i class="fa-solid fa-pen-to-square text-dark"></i>
                                        </a>                                         <!-- Block / Unblock Toggle -->
                                        <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to {{ $student->status === 'active' ? 'BLOCK' : 'ACTIVATE' }} this student account?');">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $student->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} shadow-sm d-inline-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; border-radius: 6px;"
                                                title="{{ $student->status === 'active' ? 'Block Account' : 'Activate Account' }}">
                                                <i class="fa-solid {{ $student->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
