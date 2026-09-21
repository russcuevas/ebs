@extends('layouts.app')

@section('title', 'Student Profile - ' . $student->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="border-radius: 6px;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
                </a>
                <h2 class="fw-bold mb-0" style="color: var(--ub-maroon);">Student Profile & QR Pass</h2>
            </div>
            <p class="text-muted small mb-0">Detailed student record, digital pass, and full equipment borrowing history</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-secondary bg-white shadow-sm">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Student
            </a>
            <a href="{{ route('admin.students.print', $student) }}" target="_blank" class="btn btn-ub-primary shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Print Student QR Card
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Student Identity Card & QR Pass -->
        <div class="col-lg-4">
            <div class="card card-ub overflow-hidden shadow-sm h-100" id="printableQrCard">
                <!-- University Card Header Banner -->
                <div class="py-3 px-3 text-center text-white" style="background: linear-gradient(135deg, #7B1113 0%, #580B0D 100%);">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                        <div style="width: 26px; height: 26px; background: #F5B800; color: #7B1113; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800;">
                            UB
                        </div>
                        <div class="fw-bolder" style="letter-spacing: 0.8px; font-size: 13px;">
                            UNIVERSITY OF BATANGAS
                        </div>
                    </div>
                    <div class="small" style="font-size: 11px; opacity: 0.85; letter-spacing: 0.5px;">
                        EQUIPMENT BORROWING SYSTEM &bull; STUDENT PASS
                    </div>
                </div>

                <div class="card-body p-4 text-center">
                    <!-- Student Photo -->
                    <div class="position-relative d-inline-block mb-3">
                        @if ($student->profile_photo_path && file_exists(public_path($student->profile_photo_path)))
                            <img src="{{ asset($student->profile_photo_path) }}" alt="{{ $student->name }}"
                                class="rounded-circle border border-3 border-white shadow"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow border border-3 border-white"
                                style="width: 100px; height: 100px; font-size: 36px; background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                        @endif

                        @if ($student->status === 'active')
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2" title="Active Account"></span>
                        @else
                            <span class="position-absolute bottom-0 end-0 bg-danger border border-white rounded-circle p-2" title="Blocked Account"></span>
                        @endif
                    </div>

                    <h4 class="fw-bold mb-1 text-dark">{{ $student->name }}</h4>
                    <div class="badge bg-light text-dark border px-3 py-1.5 font-monospace fw-bold mb-3" style="font-size: 13px;">
                        ID: {{ $student->student_id ?? 'N/A' }}
                    </div>

                    <!-- Digital QR Code Box -->
                    <div class="p-3 bg-white border rounded-3 shadow-sm mx-auto mb-3" style="max-width: 220px;">
                        <div class="qr-svg-wrapper">
                            {!! $qrSvg !!}
                        </div>
                        <div class="text-muted small mt-2 font-monospace" style="font-size: 10px; word-break: break-all;">
                            {{ $student->qr_code_token }}
                        </div>
                    </div>

                    <div class="text-muted small mb-3">
                        <i class="fa-solid fa-camera text-primary me-1"></i> Scan this QR at the laboratory borrowing desk
                    </div>

                    <!-- Quick Status Badges -->
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        @if ($student->email_verified_at)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                <i class="fa-solid fa-circle-check me-1"></i> Email Verified
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                <i class="fa-solid fa-clock me-1"></i> Unverified
                            </span>
                        @endif

                        @if ($student->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                <i class="fa-solid fa-shield-check me-1"></i> Active Status
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                <i class="fa-solid fa-ban me-1"></i> Blocked Status
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-footer bg-light p-3 border-top">
                    <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to {{ $student->status === 'active' ? 'BLOCK' : 'ACTIVATE' }} this student?');">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $student->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} w-100 fw-bold">
                            <i class="fa-solid {{ $student->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }} me-1"></i>
                            {{ $student->status === 'active' ? 'Block Student Account' : 'Activate Student Account' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Student Details & Borrowing History -->
        <div class="col-lg-8">
            <!-- Account & Academic Details Card -->
            <div class="card card-ub shadow-sm mb-4">
                <div class="card-header-ub py-3 px-4">
                    <span class="fw-bold fs-6" style="color: var(--ub-maroon);">
                        <i class="fa-solid fa-circle-info me-1"></i> Student Academic & Contact Information
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">Full Name</label>
                            <div class="fw-bold text-dark fs-6">{{ $student->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">Student ID Number</label>
                            <div class="fw-bold text-dark font-monospace fs-6">{{ $student->student_id ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">College / Department</label>
                            <div class="fw-bold text-dark">{{ $student->department ?? 'General' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">Official UB Gmail</label>
                            <div class="fw-bold text-dark">
                                <a href="mailto:{{ $student->email }}" class="text-decoration-none text-dark hover-maroon">
                                    {{ $student->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">Contact Phone</label>
                            <div class="fw-bold text-dark">{{ $student->phone_number ?: 'Not provided' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold d-block mb-1">Registration Date</label>
                            <div class="text-dark">{{ $student->created_at->format('F d, Y - h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowing Transactions Card -->
            <div class="card card-ub overflow-hidden shadow-sm">
                <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Borrowing History</span>
                    </div>
                    <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold">
                        {{ count($student->borrowings) }} Total Transactions
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable w-100">
                            <thead>
                                <tr>
                                    <th class="ps-4">Code / Items</th>
                                    <th>Borrowed Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Penalty</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->borrowings as $transaction)
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ route('admin.borrowings.show', $transaction) }}" class="fw-bold text-decoration-none font-monospace text-dark hover-maroon" style="font-size: 13px;">
                                                {{ $transaction->transaction_code }}
                                            </a>
                                            <div class="text-muted small mt-0.5" style="font-size: 11.5px;">
                                                @foreach ($transaction->items as $item)
                                                    <span class="badge bg-light text-dark border me-1">{{ $item->item_name }} (x{{ $item->quantity }})</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-nowrap" style="font-size: 12.5px;">
                                            {{ $transaction->borrowed_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $transaction->borrowed_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="text-nowrap" style="font-size: 12.5px;">
                                            {{ $transaction->due_date_time->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $transaction->due_date_time->format('h:i A') }}</small>
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($transaction->status === 'returned')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Returned
                                                </span>
                                            @elseif ($transaction->status === 'overdue')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                    <i class="fa-solid fa-clock me-1"></i> Ongoing
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($transaction->total_penalty > 0)
                                                @if ($transaction->penalty_status === 'paid')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 11px;">
                                                        ₱{{ number_format($transaction->total_penalty, 2) }} (Paid)
                                                    </span>
                                                @elseif ($transaction->penalty_status === 'waived')
                                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 11px;">
                                                        Waived
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                        ₱{{ number_format($transaction->total_penalty, 2) }} Unpaid
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted small">₱0.00</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <a href="{{ route('admin.borrowings.show', $transaction) }}" class="btn btn-sm btn-light border shadow-sm" style="font-size: 11.5px; border-radius: 6px;">
                                                Details <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .qr-svg-wrapper svg {
            width: 100% !important;
            height: auto !important;
            max-height: 180px;
        }
    </style>
@endpush
