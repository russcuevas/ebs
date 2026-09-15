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
            @if($transaction->items->count() > 1 && $transaction->returnedItemsCount() > 0)
                ({{ $transaction->returnedItemsCount() }}/{{ $transaction->totalItemsCount() }} Returned)
            @endif
        </a>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-ub mb-4">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2"></i> Transaction: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
                <div>
                    @if($transaction->status === 'returned')
                        <span class="badge-status-returned"><i class="fa-solid fa-check me-1"></i> Returned</span>
                    @elseif($transaction->isOverdue())
                        <span class="badge-status-overdue"><i class="fa-solid fa-triangle-exclamation me-1"></i> Overdue</span>
                        @if($transaction->returnedItemsCount() > 0)
                            <span class="badge bg-warning text-dark ms-1">Partial ({{ $transaction->returnedItemsCount() }}/{{ $transaction->totalItemsCount() }})</span>
                        @endif
                    @else
                        <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i> Ongoing</span>
                        @if($transaction->returnedItemsCount() > 0)
                            <span class="badge bg-primary text-white ms-1">Partial ({{ $transaction->returnedItemsCount() }}/{{ $transaction->totalItemsCount() }})</span>
                        @endif
                    @endif
                </div>
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
                    <button type="button" class="btn btn-sm btn-success w-100 mt-3 fw-bold" data-bs-toggle="modal" data-bs-target="#collectShowPenaltyModal">
                        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Collect Penalty Payment (₱{{ number_format($transaction->total_penalty, 2) }})
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if($transaction->total_penalty > 0 && $transaction->penalty_status === 'unpaid')
<!-- Collect Penalty Modal -->
<div class="modal fade" id="collectShowPenaltyModal" tabindex="-1" aria-labelledby="collectShowPenaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--ub-maroon);">
                <h5 class="modal-title fw-bold" id="collectShowPenaltyModalLabel">
                    <i class="fa-solid fa-hand-holding-dollar me-2 text-warning"></i> Collect Equipment Penalty
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('staff.borrowings.settle-penalty', $transaction) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Transaction Ref:</span>
                            <span class="font-monospace fw-bold" style="color: var(--ub-maroon);">{{ $transaction->reference_no }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Student:</span>
                            <span class="fw-bold text-dark">{{ $transaction->student->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-danger">Total Penalty Due:</span>
                            <span class="fs-4 fw-bold text-danger">₱{{ number_format($transaction->total_penalty, 2) }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="showCashTendered" class="form-label fw-semibold small">Cash Received / Tendered (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">₱</span>
                            <input type="number" step="0.50" min="0" class="form-control form-control-lg fw-bold" id="showCashTendered" name="amount_received" value="{{ number_format($transaction->total_penalty, 2, '.', '') }}">
                        </div>
                    </div>

                    <div class="p-2 px-3 rounded bg-success-subtle border border-success-subtle mb-3 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold text-success-emphasis">Change / Sukli:</span>
                        <span class="fw-bold fs-5 text-success" id="showChangeAmount">₱0.00</span>
                    </div>

                    <div class="mb-2">
                        <label for="showRemarks" class="form-label fw-semibold small">Receipt No. / Remarks (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="showRemarks" name="remarks" placeholder="e.g. Official Receipt # or Counter Note">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-check-circle me-1"></i> Confirm Payment Received
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    const dueAmount = {{ (float) $transaction->total_penalty }};
    $('#showCashTendered').on('input', function() {
        const tendered = parseFloat($(this).val()) || 0;
        const change = Math.max(0, tendered - dueAmount);
        $('#showChangeAmount').text('₱' + change.toFixed(2));
    });
</script>
@endpush
