@extends('layouts.app')

@section('title', 'Transaction Details - ' . $transaction->reference_no)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
    @if($transaction->status !== 'returned')
        <a href="{{ route('staff.borrowings.return', $transaction) }}" class="btn btn-sm btn-success">
            <i class="fa-solid fa-arrow-rotate-left me-1"></i> Process Return
        </a>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-ub mb-4">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2"></i> Transaction: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
                @if($transaction->status === 'returned')
                    <span class="badge-status-returned"><i class="fa-solid fa-check me-1"></i> Returned</span>
                @elseif($transaction->isOverdue())
                    <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue</span>
                @else
                    <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i> Ongoing</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Borrowing Student</small>
                        <h5 class="fw-bold mb-0 text-dark">{{ $transaction->student->name ?? 'N/A' }}</h5>
                        <div class="small text-muted">{{ $transaction->student->student_id }} &bull; {{ $transaction->student->department }}</div>
                        <div class="small text-muted">{{ $transaction->student->email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Staff Handler</small>
                        <h6 class="fw-bold mb-0 text-dark">{{ $transaction->staff->name ?? 'N/A' }}</h6>
                        <div class="small text-muted">{{ $transaction->staff->department }}</div>
                    </div>
                </div>

                <hr>

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
                                @php $n = 1; @endphp
                                @foreach($transaction->items as $item)
                                <tr>
                                    <td>{{ $n++ }}</td>
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
                            <h6 class="fw-bold small mb-2"><i class="fa-solid fa-camera me-1"></i> Proof of Borrowing</h6>
                            @if($transaction->proof_of_borrowing)
                                <a href="{{ asset($transaction->proof_of_borrowing) }}" target="_blank">
                                    <img src="{{ asset($transaction->proof_of_borrowing) }}" class="img-fluid rounded border" style="max-height: 200px; width: 100%; object-fit: cover;" alt="Proof of Borrowing">
                                </a>
                            @else
                                <div class="text-muted small py-3 text-center bg-white rounded border">
                                    No photo uploaded during borrowing.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border p-3">
                            <h6 class="fw-bold small mb-2"><i class="fa-solid fa-camera-rotate me-1"></i> Proof of Return</h6>
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

                @if($transaction->staff_notes)
                    <div class="mt-4">
                        <h6 class="fw-bold small mb-1">Staff Notes:</h6>
                        <div class="p-3 bg-light rounded border text-muted small whitespace-pre-line">{{ $transaction->staff_notes }}</div>
                    </div>
                @endif
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
                        <span class="small fw-semibold">Penalty (₱5.00/day):</span>
                        <span class="badge {{ $transaction->penalty_status === 'paid' ? 'bg-success' : ($transaction->penalty_status === 'waived' ? 'bg-secondary' : 'bg-danger') }}">
                            {{ strtoupper($transaction->penalty_status) }}
                        </span>
                    </div>
                    <div class="fs-4 fw-bold">₱{{ number_format($transaction->total_penalty, 2) }}</div>
                    @if($transaction->penalty_days > 0)
                        <small>{{ $transaction->penalty_days }} day(s) late from scheduled return time.</small>
                    @endif
                </div>

                @if($transaction->total_penalty > 0 && $transaction->penalty_status === 'unpaid')
                    <form action="{{ route('staff.borrowings.settle-penalty', $transaction) }}" method="POST" class="mt-3" onsubmit="return confirm('Confirm receipt of penalty payment?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="fa-solid fa-receipt me-1"></i> Accept Penalty Payment
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
