@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Admin Dashboard</h2>
            <p class="text-muted small mb-0">Overview and real-time status of equipment borrowing at University of Batangas
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.borrowings.index') }}" class="btn btn-outline-secondary bg-white">
                <i class="fa-solid fa-list-check me-1"></i> All Logs
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <!-- Active Borrowings -->
        <div class="col-6 col-md-3">
            <div class="card card-ub h-100 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Active Borrowed</span>
                        <i class="fa-solid fa-hand-holding-box text-warning fs-5"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $activeBorrowings }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Currently with students</small>
                </div>
            </div>
        </div>

        <!-- Overdue Borrowings -->
        <div class="col-6 col-md-3">
            <div class="card card-ub h-100 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-danger small fw-semibold">Overdue / Penalty</span>
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger">{{ $overdueBorrowings }}</h3>
                    <small class="text-danger" style="font-size: 11px;">Past due date (₱5/day)</small>
                </div>
            </div>
        </div>

        <!-- Returned Borrowings -->
        <div class="col-6 col-md-3">
            <div class="card card-ub h-100 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Returned</span>
                        <i class="fa-solid fa-circle-check text-success fs-5"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $returnedBorrowings }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Successfully completed</small>
                </div>
            </div>
        </div>

        <!-- Total Borrowings -->
        <div class="col-6 col-md-3">
            <div class="card card-ub h-100 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Total Transactions</span>
                        <i class="fa-solid fa-boxes-stacked text-primary fs-5"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalBorrowings }}</h3>
                    <small class="text-muted" style="font-size: 11px;">All recorded borrowings</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Staff Members -->
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.staff.index') }}" class="text-decoration-none">
                <div class="card card-ub p-3 h-100 hover-elevate">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            style="width: 44px; height: 44px; background: #e0f2fe; color: #0284c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-id-card-clip"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Active Staff</div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $totalStaff }}</h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Registered Students -->
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
                <div class="card card-ub p-3 h-100 hover-elevate">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            style="width: 44px; height: 44px; background: #fdf4ff; color: #c026d3; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Students</div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $totalStudents }}</h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Unpaid Penalties -->
        <div class="col-6 col-md-3">
            <div class="card card-ub p-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        style="width: 44px; height: 44px; background: #fee2e2; color: #dc2626; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-sack-xmark"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Unpaid Penalties</div>
                        <h5 class="fw-bold mb-0 text-danger">₱{{ number_format($unpaidPenalties, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collected Penalties -->
        <div class="col-6 col-md-3">
            <div class="card card-ub p-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        style="width: 44px; height: 44px; background: #ecfdf5; color: #059669; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Collected Penalties</div>
                        <h5 class="fw-bold mb-0 text-success">₱{{ number_format($collectedPenalties, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="card card-ub overflow-hidden">
        <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Recent Transactions</span>
                </div>
            </div>
            <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-3"
                style="font-size: 12px; font-weight: 600;">
                <span>View All Logs</span>
                <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Ref No.</th>
                            <th>Student</th>
                            <th>Borrowed Items</th>
                            <th>Date Borrowed</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Penalty</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTransactions as $trans)
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <a href="{{ route('admin.borrowings.show', $trans) }}"
                                        class="text-decoration-none fw-bold font-monospace d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded"
                                        style="color: var(--ub-maroon); background: #fff1f2; border: 1px solid #ffe4e6; font-size: 12px;">
                                        <i class="fa-solid fa-barcode text-muted" style="font-size: 11px;"></i>
                                        <span>{{ $trans->reference_no }}</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                                            style="width: 34px; height: 34px; font-size: 13px; background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);">
                                            {{ strtoupper(substr($trans->student->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-nowrap" style="font-size: 13.5px;">{{ $trans->student->name ?? 'N/A' }}</div>
                                            <div class="d-flex align-items-center gap-1.5 text-muted small text-nowrap" style="font-size: 11.5px;">
                                                <span class="badge bg-light text-secondary border px-1.5 py-0.5 fw-semibold">{{ $trans->student->student_id ?? 'N/A' }}</span>
                                                <span class="text-truncate" style="max-width: 140px;" title="{{ $trans->student->department ?? '' }}">{{ $trans->student->department ?? '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($trans->items as $item)
                                            <div class="d-flex align-items-center gap-1.5 text-nowrap">
                                                <span class="badge bg-light text-dark border px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1"
                                                    style="font-size: 12px; border-color: #e2e8f0 !important;">
                                                    <i class="fa-solid fa-box text-muted" style="font-size: 10px;"></i>
                                                    {{ $item->item_name }}
                                                </span>
                                                @if ($item->item_location)
                                                    <span class="text-muted small" style="font-size: 11px;">({{ $item->item_location }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                        {{ $trans->borrow_date_time->format('M d, Y') }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 11.5px;">
                                        <i class="fa-regular fa-clock me-1 opacity-75"></i>{{ $trans->borrow_date_time->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="fw-semibold {{ $trans->isOverdue() ? 'text-danger fw-bold' : 'text-dark' }}" style="font-size: 12.5px;">
                                        {{ $trans->due_date_time->format('M d, Y') }}
                                    </div>
                                    <div class="{{ $trans->isOverdue() ? 'text-danger fw-semibold' : 'text-muted' }} small" style="font-size: 11.5px;">
                                        <i class="fa-regular fa-clock me-1 opacity-75"></i>{{ $trans->due_date_time->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    @if ($trans->status === 'returned')
                                        <span class="badge-status-returned"><i class="fa-solid fa-circle-check"></i> Returned</span>
                                    @elseif($trans->status === 'overdue' || $trans->isOverdue())
                                        <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation"></i> Overdue</span>
                                    @else
                                        <span class="badge-status-ongoing"><i class="fa-solid fa-clock"></i> Ongoing</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if ($trans->total_penalty > 0)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold {{ $trans->penalty_status === 'paid' ? 'text-success' : 'text-danger' }}" style="font-size: 13px;">
                                                ₱{{ number_format($trans->total_penalty, 2) }}
                                            </span>
                                            <div>
                                                @if ($trans->penalty_status === 'paid')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.3px;">PAID</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.3px;">UNPAID</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 13px;">—</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <a href="{{ route('admin.borrowings.show', $trans) }}"
                                        class="btn btn-sm btn-light border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px; border-radius: 6px;"
                                        title="View Details">
                                        <i class="fa-solid fa-eye text-dark"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
