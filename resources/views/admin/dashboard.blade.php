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
            <div class="card card-ub p-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        style="width: 44px; height: 44px; background: #e0f2fe; color: #0284c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Staff</div>
                        <h5 class="fw-bold mb-0">{{ $totalStaff }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registered Students -->
        <div class="col-6 col-md-3">
            <div class="card card-ub p-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        style="width: 44px; height: 44px; background: #fdf4ff; color: #c026d3; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Students</div>
                        <h5 class="fw-bold mb-0">{{ $totalStudents }}</h5>
                    </div>
                </div>
            </div>
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
    <div class="card card-ub">
        <div class="card-header-ub d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Transactions</span>
            <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-link text-decoration-none"
                style="color: var(--ub-maroon);">View All &rarr;</a>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Ref No.</th>
                            <th>Student</th>
                            <th>Items</th>
                            <th>Date Borrowed</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Penalty</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentTransactions as $trans)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold" style="color: var(--ub-maroon);">
                                    <a href="{{ route('admin.borrowings.show', $trans) }}" class="text-decoration-none" style="color: var(--ub-maroon);">
                                        {{ $trans->reference_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $trans->student->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $trans->student->student_id ?? '' }} &bull;
                                        {{ $trans->student->department ?? '' }}</small>
                                </td>
                                <td>
                                    @foreach ($trans->items as $item)
                                        <div><i class="fa-solid fa-circle-dot me-1 text-secondary"
                                                style="font-size: 8px;"></i> {{ $item->item_name }} <small
                                                class="text-muted">({{ $item->item_location }})</small></div>
                                    @endforeach
                                </td>
                                <td><small>{{ $trans->borrow_date_time->format('M d, Y h:i A') }}</small></td>
                                <td>
                                    <small class="{{ $trans->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                        {{ $trans->due_date_time->format('M d, Y h:i A') }}
                                    </small>
                                </td>
                                <td>
                                    @if ($trans->status === 'returned')
                                        <span class="badge-status-returned"><i class="fa-solid fa-check me-1"></i>
                                            Returned</span>
                                    @elseif($trans->status === 'overdue' || $trans->isOverdue())
                                        <span class="badge-status-overdue"><i
                                                class="fa-solid fa-triangle-exclamation me-1"></i> Overdue</span>
                                    @else
                                        <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i>
                                            Ongoing</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($trans->total_penalty > 0)
                                        <div
                                            class="fw-bold {{ $trans->penalty_status === 'paid' ? 'text-success' : 'text-danger' }}">
                                            ₱{{ number_format($trans->total_penalty, 2) }}
                                            <small class="d-block"
                                                style="font-size: 10px;">({{ strtoupper($trans->penalty_status) }})</small>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.borrowings.show', $trans) }}"
                                        class="btn btn-sm btn-outline-secondary" title="View details">
                                        <i class="fa-solid fa-eye"></i>
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
