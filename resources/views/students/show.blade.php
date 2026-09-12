@extends('layouts.app')

@section('title', 'Borrowing Details - ' . $transaction->reference_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('student.history') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to History
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-ub mb-4">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-invoice me-2"></i> Transaction: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
                @if($transaction->status === 'returned')
                    <span class="badge-status-returned"><i class="fa-solid fa-check me-1"></i> Returned</span>
                @elseif($transaction->isOverdue())
                    <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue</span>
                @else
                    <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i> Ongoing</span>
                @endif
            </div>
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: var(--ub-maroon);"><i class="fa-solid fa-boxes-stacked me-2"></i> Borrowed Items</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($transaction->items) > 0)
                                @php $c = 1; @endphp
                                @foreach($transaction->items as $item)
                                <tr>
                                    <td>{{ $c++ }}</td>
                                    <td class="fw-bold">{{ $item->item_name }}</td>
                                    <td>{{ $item->item_location }}</td>
                                    <td>
                                        @if($item->status === 'returned')
                                            <span class="badge bg-success-subtle text-success">Returned</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis">Currently Borrowed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Proof Photos -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-light border p-3">
                            <h6 class="fw-bold small mb-2"><i class="fa-solid fa-camera me-1"></i> Photo Upon Borrowing</h6>
                            @if($transaction->proof_of_borrowing)
                                <a href="{{ asset($transaction->proof_of_borrowing) }}" target="_blank">
                                    <img src="{{ asset($transaction->proof_of_borrowing) }}" class="img-fluid rounded border" style="max-height: 200px; width: 100%; object-fit: cover;" alt="Proof of Borrowing">
                                </a>
                            @else
                                <div class="text-muted small py-3 text-center bg-white rounded border">
                                    No photo uploaded upon borrowing.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border p-3">
                            <h6 class="fw-bold small mb-2"><i class="fa-solid fa-camera-rotate me-1"></i> Photo Upon Return</h6>
                            @if($transaction->proof_of_return)
                                <a href="{{ asset($transaction->proof_of_return) }}" target="_blank">
                                    <img src="{{ asset($transaction->proof_of_return) }}" class="img-fluid rounded border" style="max-height: 200px; width: 100%; object-fit: cover;" alt="Proof of Return">
                                </a>
                            @else
                                <div class="text-muted small py-3 text-center bg-white rounded border">
                                    Equipment not yet returned.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-ub mb-4">
            <div class="card-header-ub">
                <i class="fa-solid fa-clock me-2"></i> Schedule & Penalty
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <small class="text-muted d-block">Borrowed Date & Time</small>
                    <div class="fw-bold">{{ $transaction->borrow_date_time->format('M d, Y h:i A') }}</div>
                    <small class="text-muted">Handled by: {{ $transaction->staff->name ?? 'Staff' }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Scheduled Due Date & Time</small>
                    <div class="fw-bold {{ $transaction->isOverdue() ? 'text-danger' : 'text-dark' }}">
                        {{ $transaction->due_date_time->format('M d, Y h:i A') }}
                    </div>
                </div>
                @if($transaction->return_date_time)
                <div class="mb-3">
                    <small class="text-muted d-block">Actual Return Date & Time</small>
                    <div class="fw-bold text-success">{{ $transaction->return_date_time->format('M d, Y h:i A') }}</div>
                </div>
                @endif

                <hr>

                <div class="p-3 rounded {{ $transaction->total_penalty > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-muted' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">Penalty Policy (₱5/day):</span>
                        <span class="badge {{ $transaction->penalty_status === 'paid' ? 'bg-success' : ($transaction->penalty_status === 'waived' ? 'bg-secondary' : 'bg-danger') }}">
                            {{ strtoupper($transaction->penalty_status) }}
                        </span>
                    </div>
                    <div class="fs-4 fw-bold">₱{{ number_format($transaction->total_penalty, 2) }}</div>
                    <small style="font-size: 11px;">
                        @if($transaction->penalty_days > 0)
                            {{ $transaction->penalty_days }} day(s) late from scheduled return time.
                        @else
                            No penalty applicable.
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
