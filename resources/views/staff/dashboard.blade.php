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
    <div class="col-6 col-lg">
        <div class="card card-ub border-start border-4 border-warning h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Ongoing / Active</span>
                <i class="fa-solid fa-clock text-warning fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $activeBorrowings }}</h3>
            <small class="text-muted" style="font-size: 11px;">Active borrowed</small>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="card card-ub border-start border-4 border-danger h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-danger small fw-semibold">Overdue (Overdue Fines)</span>
                <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-danger">{{ $overdueBorrowings }}</h3>
            <small class="text-danger" style="font-size: 11px;">Accruing penalties</small>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="card card-ub border-start border-4 border-danger h-100 p-3 bg-danger-subtle bg-opacity-10">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-danger small fw-semibold">Unpaid Penalties</span>
                <i class="fa-solid fa-hand-holding-dollar text-danger fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-danger">₱{{ number_format($unpaidPenaltiesTotal, 2) }}</h3>
            <small class="text-muted" style="font-size: 11px;">{{ $unpaidPenaltiesCount }} uncollected</small>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="card card-ub border-start border-4 border-success h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Returned</span>
                <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $returnedBorrowings }}</h3>
            <small class="text-muted" style="font-size: 11px;">Completed returns</small>
        </div>
    </div>

    <div class="col-12 col-lg">
        <div class="card card-ub border-start border-4 border-primary h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold">Total Records</span>
                <i class="fa-solid fa-boxes-stacked text-primary fs-5"></i>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalTransactions }}</h3>
            <small class="text-muted" style="font-size: 11px;">All transactions</small>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card card-ub">
    <div class="card-header-ub d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="fa-solid fa-clock-rotate-left me-2"></i> Borrowing History & Logs</span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('staff.dashboard') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">All</a>
            <a href="{{ route('staff.dashboard', ['status' => 'ongoing']) }}" class="btn btn-sm {{ request('status') === 'ongoing' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' }}">Ongoing</a>
            <a href="{{ route('staff.dashboard', ['status' => 'overdue']) }}" class="btn btn-sm {{ request('status') === 'overdue' ? 'btn-danger fw-bold' : 'btn-outline-secondary bg-white' }}">Overdue</a>
            <a href="{{ route('staff.dashboard', ['status' => 'unpaid_penalty']) }}" class="btn btn-sm {{ request('status') === 'unpaid_penalty' ? 'btn-danger fw-bold' : 'btn-outline-secondary bg-white' }}">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Unpaid Penalties @if($unpaidPenaltiesCount > 0)<span class="badge bg-danger ms-1">{{ $unpaidPenaltiesCount }}</span>@endif
            </a>
            <a href="{{ route('staff.dashboard', ['status' => 'returned']) }}" class="btn btn-sm {{ request('status') === 'returned' ? 'btn-success fw-bold' : 'btn-outline-secondary bg-white' }}">Returned</a>
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
                                        @if($item->status === 'returned')
                                            <span class="badge bg-success-subtle text-success ms-1" style="font-size: 9px;"><i class="fa-solid fa-check"></i> Returned</span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($trans->items->count() > 1 && $trans->status !== 'returned' && $trans->returnedItemsCount() > 0)
                                    <div class="mt-1">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 10px;">
                                            <i class="fa-solid fa-list-check me-1"></i> {{ $trans->returnedItemsCount() }}/{{ $trans->totalItemsCount() }} Returned
                                        </span>
                                    </div>
                                @endif
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
                                    @if($trans->returnedItemsCount() > 0)
                                        <small class="d-block text-warning fw-bold mt-1" style="font-size: 10px;">Partial ({{ $trans->returnedItemsCount() }}/{{ $trans->totalItemsCount() }})</small>
                                    @endif
                                @else
                                    <span class="badge-status-ongoing"><i class="fa-solid fa-clock me-1"></i> Ongoing</span>
                                    @if($trans->returnedItemsCount() > 0)
                                        <small class="d-block text-primary fw-bold mt-1" style="font-size: 10px;">Partial ({{ $trans->returnedItemsCount() }}/{{ $trans->totalItemsCount() }})</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($trans->total_penalty > 0)
                                    <div class="fw-bold {{ $trans->penalty_status === 'paid' ? 'text-success' : 'text-danger' }}">
                                        ₱{{ number_format($trans->total_penalty, 2) }}
                                        <span class="badge {{ $trans->penalty_status === 'paid' ? 'bg-success' : 'bg-danger' }} ms-1" style="font-size: 10px;">
                                            {{ strtoupper($trans->penalty_status) }}
                                        </span>
                                    </div>
                                    @if($trans->penalty_status === 'unpaid')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success mt-1 py-0 px-2 fw-semibold" 
                                                style="font-size: 11px;" 
                                                onclick="openCollectModal('{{ route('staff.borrowings.settle-penalty', $trans) }}', '{{ $trans->reference_no }}', '{{ addslashes($trans->student->name ?? 'Student') }}', {{ (float) $trans->total_penalty }})"
                                                title="Collect Cash Penalty Payment">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Collect
                                        </button>
                                    @else
                                        <small class="text-muted d-block" style="font-size: 10px;">
                                            Paid {{ $trans->penalty_paid_at ? $trans->penalty_paid_at->format('M d') : '' }}
                                        </small>
                                    @endif
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
                                            @if($trans->items->count() > 1 && $trans->returnedItemsCount() > 0)
                                                ({{ $trans->returnedItemsCount() }}/{{ $trans->totalItemsCount() }})
                                            @endif
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

<!-- Collect Penalty Modal -->
<div class="modal fade" id="collectPenaltyModal" tabindex="-1" aria-labelledby="collectPenaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--ub-maroon);">
                <h5 class="modal-title fw-bold" id="collectPenaltyModalLabel">
                    <i class="fa-solid fa-hand-holding-dollar me-2 text-warning"></i> Collect Equipment Penalty
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="collectPenaltyForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Transaction Ref:</span>
                            <span class="font-monospace fw-bold" id="modalRefNo" style="color: var(--ub-maroon);"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Borrowing Student:</span>
                            <span class="fw-bold text-dark" id="modalStudentName"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-danger">Total Penalty Due:</span>
                            <span class="fs-4 fw-bold text-danger" id="modalPenaltyAmount">₱0.00</span>
                        </div>
                    </div>

                    <!-- Cash Calculation Box -->
                    <div class="mb-3">
                        <label for="modalCashTendered" class="form-label fw-semibold small">Cash Tendered / Amount Received (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">₱</span>
                            <input type="number" step="0.50" min="0" class="form-control form-control-lg fw-bold" id="modalCashTendered" name="amount_received" placeholder="0.00">
                        </div>
                    </div>

                    <div class="p-2 px-3 rounded bg-success-subtle border border-success-subtle mb-3 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold text-success-emphasis">Change / Sukli:</span>
                        <span class="fw-bold fs-5 text-success" id="modalChangeAmount">₱0.00</span>
                    </div>

                    <!-- Optional Receipt / Remarks -->
                    <div class="mb-2">
                        <label for="modalRemarks" class="form-label fw-semibold small">Official Receipt No. / Remarks (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="modalRemarks" name="remarks" placeholder="e.g. Cash Receipt # or Staff Counter Note">
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
@endsection

@push('scripts')
<script>
    let currentPenaltyAmount = 0;

    function openCollectModal(actionUrl, refNo, studentName, penaltyAmount) {
        currentPenaltyAmount = parseFloat(penaltyAmount) || 0;
        $('#collectPenaltyForm').attr('action', actionUrl);
        $('#modalRefNo').text(refNo);
        $('#modalStudentName').text(studentName);
        $('#modalPenaltyAmount').text('₱' + currentPenaltyAmount.toFixed(2));
        $('#modalCashTendered').val(currentPenaltyAmount.toFixed(2));
        $('#modalChangeAmount').text('₱0.00');
        $('#modalRemarks').val('');

        const modal = new bootstrap.Modal(document.getElementById('collectPenaltyModal'));
        modal.show();
    }

    $('#modalCashTendered').on('input', function() {
        const tendered = parseFloat($(this).val()) || 0;
        const change = Math.max(0, tendered - currentPenaltyAmount);
        $('#modalChangeAmount').text('₱' + change.toFixed(2));
    });
</script>
@endpush
