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
<div class="card card-ub overflow-hidden">
    <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <span class="fw-bold fs-6" style="color: var(--ub-maroon);">My Transactions List</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100 mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Ref No.</th>
                        <th>Borrowed Items</th>
                        <th>Staff Handler</th>
                        <th>Date Borrowed</th>
                        <th>Due Date</th>
                        <th>Date Returned</th>
                        <th>Status</th>
                        <th>Penalty (₱5/day)</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($borrowings) > 0)
                        @foreach($borrowings as $trans)
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <a href="{{ route('student.borrowings.show', $trans) }}"
                                    class="text-decoration-none fw-bold font-monospace d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded"
                                    style="color: var(--ub-maroon); background: #fff1f2; border: 1px solid #ffe4e6; font-size: 12px;">
                                    <i class="fa-solid fa-barcode text-muted" style="font-size: 11px;"></i>
                                    <span>{{ $trans->reference_no }}</span>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($trans->items as $item)
                                        <div class="d-flex align-items-center gap-1.5 text-nowrap">
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1"
                                                style="font-size: 12px; border-color: #e2e8f0 !important;">
                                                <i class="fa-solid fa-cube text-muted" style="font-size: 10px;"></i>
                                                {{ $item->item_name }}
                                            </span>
                                            @if($item->item_location)
                                                <span class="text-muted small" style="font-size: 11px;">({{ $item->item_location }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $trans->staff->name ?? 'Staff' }}</div>
                                <div class="text-muted small" style="font-size: 11px;">{{ $trans->staff->department ?? 'General' }}</div>
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
                                @if($trans->return_date_time)
                                    <div class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $trans->return_date_time->format('M d, Y') }}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">
                                        <i class="fa-regular fa-clock me-1 opacity-75"></i>{{ $trans->return_date_time->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($trans->status === 'returned')
                                    <span class="badge-status-returned"><i class="fa-solid fa-circle-check"></i> Returned</span>
                                @elseif($trans->status === 'overdue' || $trans->isOverdue())
                                    <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation"></i> Overdue</span>
                                @else
                                    <span class="badge-status-ongoing"><i class="fa-solid fa-clock"></i> Ongoing</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($trans->total_penalty > 0)
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold {{ $trans->penalty_status === 'paid' ? 'text-success' : 'text-danger' }}" style="font-size: 13px;">
                                            ₱{{ number_format($trans->total_penalty, 2) }}
                                        </span>
                                        <div>
                                            @if($trans->penalty_status === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700;">PAID</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700;">UNPAID ({{ $trans->penalty_days }}d)</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 13px;">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <a href="{{ route('student.borrowings.show', $trans) }}"
                                    class="btn btn-sm btn-light border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; border-radius: 6px;"
                                    title="View details">
                                    <i class="fa-solid fa-eye text-dark"></i>
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
