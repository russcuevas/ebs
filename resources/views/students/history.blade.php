@extends('layouts.app')

@section('title', 'My Borrowing History')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">My Borrowing History</h2>
        <p class="text-muted small mb-0">Record and timeline of all your equipment borrowings at University of Batangas</p>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-qrcode me-1"></i> View My QR Code
    </a>
</div>

<!-- Filter Tabs -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('student.history') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">
        All
    </a>
    <a href="{{ route('student.history', ['status' => 'ongoing']) }}" class="btn btn-sm {{ request('status') === 'ongoing' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-clock me-1"></i> Ongoing
    </a>
    <a href="{{ route('student.history', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') === 'overdue' ? 'btn-danger fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue (With Penalty)
    </a>
    <a href="{{ route('student.history', ['status' => 'returned']) }}" class="btn btn-sm {{ request('status') === 'returned' ? 'btn-success fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-check me-1"></i> Returned
    </a>
</div>

<!-- History DataTable -->
<div class="card card-ub shadow-sm">
    <div class="card-header-ub">
        <i class="fa-solid fa-list-check me-2"></i> My Transactions List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Borrowed Items</th>
                        <th>Staff Handler</th>
                        <th>Date Borrowed</th>
                        <th>Due Date</th>
                        <th>Date Returned</th>
                        <th>Status</th>
                        <th>Penalty (₱5/day)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($borrowings) > 0)
                        @foreach($borrowings as $trans)
                        <tr>
                            <td class="font-monospace fw-bold">
                                <a href="{{ route('student.borrowings.show', $trans) }}" style="color: var(--ub-maroon);" class="text-decoration-none">
                                    {{ $trans->reference_no }}
                                </a>
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
                            <td>
                                <small class="fw-semibold">{{ $trans->staff->name ?? 'Staff' }}</small>
                                <small class="text-muted d-block" style="font-size: 10px;">{{ $trans->staff->department ?? '' }}</small>
                            </td>
                            <td><small>{{ $trans->borrow_date_time->format('M d, Y h:i A') }}</small></td>
                            <td>
                                <small class="{{ $trans->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                    {{ $trans->due_date_time->format('M d, Y h:i A') }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    {{ $trans->return_date_time ? $trans->return_date_time->format('M d, Y h:i A') : '—' }}
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
                                        <small class="d-block" style="font-size: 10px;">
                                            @if($trans->penalty_status === 'paid')
                                                (Paid)
                                            @else
                                                ({{ $trans->penalty_days }} day(s) late)
                                            @endif
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('student.borrowings.show', $trans) }}" class="btn btn-sm btn-outline-primary" title="View details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
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
