@extends('layouts.app')

@section('title', 'Borrowing Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">All Borrowing Logs</h2>
        <p class="text-muted small mb-0">Comprehensive record of all equipment borrowed across the university</p>
    </div>
</div>

<!-- Status Filters -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">
        All
    </a>
    <a href="{{ route('admin.borrowings.index', ['status' => 'ongoing']) }}" class="btn btn-sm {{ request('status') === 'ongoing' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-clock me-1"></i> Ongoing
    </a>
    <a href="{{ route('admin.borrowings.index', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') === 'overdue' ? 'btn-danger fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue (With Penalty)
    </a>
    <a href="{{ route('admin.borrowings.index', ['status' => 'returned']) }}" class="btn btn-sm {{ request('status') === 'returned' ? 'btn-success fw-bold' : 'btn-outline-secondary bg-white' }}">
        <i class="fa-solid fa-check me-1"></i> Returned
    </a>
</div>

<div class="card card-ub">
    <div class="card-header-ub d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-list-check me-2"></i> Transactions List</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Student</th>
                        <th>Items</th>
                        <th>Staff / Handler</th>
                        <th>Date Borrowed</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Penalty</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($transactions) > 0)
                        @foreach($transactions as $trans)
                        <tr>
                            <td class="font-monospace fw-bold" style="color: var(--ub-maroon);">
                                <a href="{{ route('admin.borrowings.show', $trans) }}" class="text-decoration-none" style="color: var(--ub-maroon);">
                                    {{ $trans->reference_no }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $trans->student->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $trans->student->student_id ?? '' }}</div>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $trans->student->email ?? '' }}</small>
                            </td>
                            <td>
                                @foreach($trans->items as $item)
                                    <div class="text-nowrap mb-1">
                                        <i class="fa-solid fa-box text-secondary me-1" style="font-size: 11px;"></i> 
                                        <strong>{{ $item->item_name }}</strong> 
                                        <small class="text-muted">({{ $item->item_location }})</small>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <small class="fw-semibold text-dark">{{ $trans->staff->name ?? 'System' }}</small>
                                <small class="text-muted d-block" style="font-size: 10px;">{{ $trans->staff->department ?? '' }}</small>
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
                                    <a href="{{ route('admin.borrowings.show', $trans) }}" class="btn btn-outline-primary" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($trans->status !== 'returned')
                                        <form action="{{ route('admin.borrowings.send-reminder', $trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Send real-time email reminder to the student?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" title="Send Email Reminder">
                                                <i class="fa-solid fa-envelope"></i>
                                            </button>
                                        </form>
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
