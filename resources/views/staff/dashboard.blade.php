@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Staff Dashboard</h2>
        <p class="text-muted small mb-0">Manage equipment lending, returns, and student QR scanning</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.scanner.index') }}" class="btn btn-ub-gold">
            <i class="fa-solid fa-qrcode me-1"></i> Scan Student QR
        </a>
        <a href="{{ route('staff.borrowings.create') }}" class="btn btn-ub-primary">
            <i class="fa-solid fa-plus-circle me-1"></i> New Borrowing
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-ub border-start border-4 border-warning h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Ongoing / Active</span>
                <i class="fa-solid fa-clock text-warning fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $activeBorrowings }}</h3>
            <small class="text-muted" style="font-size: 11px;">Currently borrowed</small>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-ub border-start border-4 border-danger h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-danger small fw-semibold">Overdue (With Penalty)</span>
                <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-danger">{{ $overdueBorrowings }}</h3>
            <small class="text-danger" style="font-size: 11px;">Requires ₱5.00/day settlement</small>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-ub border-start border-4 border-success h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Returned</span>
                <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $returnedBorrowings }}</h3>
            <small class="text-muted" style="font-size: 11px;">Completed returns</small>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-ub border-start border-4 border-primary h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Total Records</span>
                <i class="fa-solid fa-boxes-stacked text-primary fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalTransactions }}</h3>
            <small class="text-muted" style="font-size: 11px;">Total transactions</small>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card card-ub">
    <div class="card-header-ub d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="fa-solid fa-clock-rotate-left me-2"></i> Borrowing History & Logs</span>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">All</a>
            <a href="{{ route('staff.dashboard', ['status' => 'ongoing']) }}" class="btn btn-sm {{ request('status') === 'ongoing' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Ongoing</a>
            <a href="{{ route('staff.dashboard', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') === 'overdue' ? 'btn-danger' : 'btn-outline-secondary' }}">Overdue</a>
            <a href="{{ route('staff.dashboard', ['status' => 'returned']) }}" class="btn btn-sm {{ request('status') === 'returned' ? 'btn-success' : 'btn-outline-secondary' }}">Returned</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Student</th>
                        <th>Borrowed Items</th>
                        <th>Date Borrowed</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Penalty (₱5/day)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($transactions) > 0)
                        @foreach($transactions as $trans)
                        <tr>
                            <td class="font-monospace fw-bold" style="color: var(--ub-maroon);">
                                <a href="{{ route('staff.borrowings.show', $trans) }}" style="color: var(--ub-maroon);" class="text-decoration-none">
                                    {{ $trans->reference_no }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $trans->student->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $trans->student->student_id ?? '' }} &bull; {{ $trans->student->department ?? '' }}</small>
                            </td>
                            <td>
                                @foreach($trans->items as $item)
                                    <div>
                                        <i class="fa-solid fa-cube text-secondary me-1" style="font-size: 11px;"></i>
                                        <strong>{{ $item->item_name }}</strong> 
                                        <small class="text-muted">({{ $item->item_location }})</small>
                                    </div>
                                @endforeach
                            </td>
                            <td><small>{{ $trans->borrow_date_time->format('M d, Y h:i A') }}</small></td>
                            <td>
                                <small class="{{ $trans->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                    {{ $trans->due_date_time->format('M d, Y h:i A') }}
                                </small>
                            </td>
                            <td>
                                @if($trans->status === 'returned')
                                    <span class="badge-status-returned"><i class="fa-solid fa-check me-1"></i> Returned</span>
                                @elseif($trans->status === 'overdue' || $trans->isOverdue())
                                    <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue</span>
                                @else
                                    <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i> Ongoing</span>
                                @endif
                            </td>
                            <td>
                                @if($trans->total_penalty > 0)
                                    <div class="fw-bold {{ $trans->penalty_status === 'paid' ? 'text-success' : 'text-danger' }}">
                                        ₱{{ number_format($trans->total_penalty, 2) }}
                                        <small class="d-block" style="font-size: 10px;">({{ strtoupper($trans->penalty_status) }})</small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('staff.borrowings.show', $trans) }}" class="btn btn-outline-primary" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($trans->status !== 'returned')
                                        <a href="{{ route('staff.borrowings.return', $trans) }}" class="btn btn-success" title="Process Return">
                                            <i class="fa-solid fa-arrow-rotate-left me-1"></i> Return
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
