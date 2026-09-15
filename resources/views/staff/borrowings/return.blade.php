@extends('layouts.app')

@section('title', 'Process Return - ' . $transaction->reference_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card card-ub shadow-sm">
            <div class="card-header-ub d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><i class="fa-solid fa-arrow-rotate-left me-2"></i> Equipment Return: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
                <div class="d-flex align-items-center gap-2">
                    @if($transaction->penalty_status === 'paid')
                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Penalty Paid</span>
                    @elseif($penaltyInfo['amount'] > 0 || $transaction->total_penalty > 0)
                        <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Penalty Unpaid (₱{{ number_format($penaltyInfo['amount'] > 0 ? $penaltyInfo['amount'] : $transaction->total_penalty, 2) }})</span>
                    @endif
                    <span class="badge {{ $transaction->isOverdue() ? 'bg-danger' : 'bg-warning text-dark' }}">
                        {{ $transaction->isOverdue() ? 'Overdue' : 'Currently Borrowed' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Student & Borrow Details Summary -->
                <div class="row g-3 p-3 bg-light rounded border mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Returning Student:</small>
                        <h5 class="fw-bold mb-0 text-dark">{{ $transaction->student->name ?? 'N/A' }}</h5>
                        <div class="small text-muted">{{ $transaction->student->student_id }} &bull; {{ $transaction->student->department }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Scheduled Due Date & Time:</small>
                        <div class="fw-bold {{ $transaction->isOverdue() ? 'text-danger fs-6' : 'text-dark' }}">
                            {{ $transaction->due_date_time->format('M d, Y h:i A') }}
                        </div>
                        <small class="text-muted">Borrowed on: {{ $transaction->borrow_date_time->format('M d, Y h:i A') }}</small>
                    </div>
                </div>

                @php
                    $totalCount = $transaction->totalItemsCount();
                    $returnedCount = $transaction->returnedItemsCount();
                    $pendingCount = $transaction->pendingItemsCount();
                    $percent = $transaction->returnProgressPercent();
                @endphp

                <!-- Return Progress Bar & Stats -->
                <div class="p-3 bg-white border rounded mb-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <span class="fw-bold text-dark">
                            <i class="fa-solid fa-list-check me-1 text-primary"></i> Return Progress:
                            <span class="font-monospace text-primary fs-5">{{ $returnedCount }}/{{ $totalCount }}</span> Items Returned
                        </span>
                        <span class="badge {{ $returnedCount === $totalCount ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill fw-bold">
                            {{ $percent }}% Complete
                        </span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped {{ $returnedCount === $totalCount ? 'bg-success' : 'bg-warning' }}" 
                             role="progressbar" 
                             style="width: {{ $percent }}%;" 
                             aria-valuenow="{{ $percent }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                        <span><i class="fa-solid fa-check-circle text-success me-1"></i> <strong>{{ $returnedCount }}</strong> already returned</span>
                        <span><i class="fa-solid fa-clock text-warning me-1"></i> <strong>{{ $pendingCount }}</strong> pending</span>
                    </div>
                </div>

                <!-- Penalty Box: Displays PAID if settled, or OVERDUE alert if unpaid -->
                @if($transaction->penalty_status === 'paid')
                    <!-- Penalty Already Settled (Green Box) -->
                    <div class="alert alert-success border-2 border-success p-3 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fa-solid fa-circle-check fs-2 text-success flex-shrink-0"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h5 class="fw-bold text-success mb-0">
                                        <i class="fa-solid fa-receipt me-1"></i> PENALTY STATUS: PAID
                                    </h5>
                                    <span class="badge bg-success px-3 py-1 fs-6">
                                        <i class="fa-solid fa-check-circle me-1"></i> PAID
                                    </span>
                                </div>
                                <div class="fs-4 fw-bold text-success my-1">
                                    ₱{{ number_format($transaction->total_penalty, 2) }}
                                    <small class="fs-6 text-muted fw-normal">({{ $transaction->penalty_days }} day(s) late settled)</small>
                                </div>
                                <small class="text-muted d-block">
                                    Penalty payment was officially recorded on <strong>{{ $transaction->penalty_paid_at ? $transaction->penalty_paid_at->format('M d, Y h:i A') : 'N/A' }}</strong>. No outstanding fine remaining.
                                </small>
                            </div>
                        </div>
                    </div>
                @elseif($penaltyInfo['amount'] > 0)
                    <!-- Penalty Unpaid / Overdue (Red Box) -->
                    <div class="alert alert-danger border-2 border-danger p-3 mb-4 shadow-sm">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation fs-2 text-danger flex-shrink-0"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h5 class="fw-bold text-danger mb-0">OVERDUE TRANSACTION (PENALTY ACCRUING)</h5>
                                    <span class="badge bg-danger px-3 py-1 fs-6">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> UNPAID
                                    </span>
                                </div>
                                <p class="small mb-1 mt-1">
                                    Equipment return is late by <strong>{{ $penaltyInfo['days'] }} day(s)</strong> from the scheduled due date.
                                </p>
                                <div class="fs-4 fw-bold text-danger">
                                    Penalty: ₱{{ number_format($penaltyInfo['amount'], 2) }}
                                    <small class="fs-6 text-muted fw-normal">(₱5.00 per day)</small>
                                </div>
                                <small class="text-dark d-block mt-2">
                                    <strong>Important:</strong> If only some items are returned today, the penalty will continue counting daily on unreturned items until all {{ $totalCount }}/{{ $totalCount }} items are fully returned.
                                </small>

                                <!-- Quick Button to Collect Penalty right now -->
                                <div class="mt-3 pt-2 border-top border-danger-subtle d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="small text-danger fw-semibold">Want to collect cash payment right now?</span>
                                    <button type="button" 
                                            class="btn btn-sm btn-success fw-bold" 
                                            onclick="openReturnCollectModal('{{ route('staff.borrowings.settle-penalty', $transaction) }}', '{{ $transaction->reference_no }}', '{{ addslashes($transaction->student->name ?? 'Student') }}', {{ (float) $penaltyInfo['amount'] }})">
                                        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Collect Penalty (₱{{ number_format($penaltyInfo['amount'], 2) }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success py-2 px-3 small mb-4">
                        <i class="fa-solid fa-circle-check me-1"></i> <strong>On Time:</strong> Currently within scheduled deadline. Fines begin if items remain unreturned past due date.
                    </div>
                @endif

                <!-- Return Processing Form -->
                <form action="{{ route('staff.borrowings.process-return', $transaction) }}" method="POST" enctype="multipart/form-data" novalidate id="returnForm">
                    @csrf

                    <!-- Borrowed Items Checklist Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="fa-solid fa-boxes-stacked me-1"></i> Check Items to Return in this Batch <span class="text-danger">*</span>
                            </label>
                            @if($pendingCount > 1)
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" id="btnSelectAllPending" style="font-size: 11px;">
                                        <i class="fa-solid fa-check-double me-1"></i> Check All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnUncheckAllPending" style="font-size: 11px;">
                                        <i class="fa-solid fa-xmark me-1"></i> Uncheck All
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="list-group shadow-sm" id="itemsChecklist">
                            @foreach($transaction->items as $item)
                                @if($item->status === 'returned')
                                    <!-- Already Returned Item (Disabled) -->
                                    <div class="list-group-item bg-light border d-flex justify-content-between align-items-center p-3 opacity-75">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-success fs-5">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-secondary text-decoration-line-through">
                                                    {{ $item->item_name }}
                                                </h6>
                                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> {{ $item->item_location }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Already Returned</span>
                                            <small class="d-block text-muted" style="font-size: 10px;">{{ $item->updated_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                @else
                                    <!-- Pending Item (Selectable Checkbox) -->
                                    <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border item-checkbox-label" style="cursor: pointer;" for="item_checkbox_{{ $item->id }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <input class="form-check-input pending-item-checkbox my-0 fs-5" 
                                                   type="checkbox" 
                                                   name="returned_item_ids[]" 
                                                   value="{{ $item->id }}" 
                                                   id="item_checkbox_{{ $item->id }}" 
                                                   checked>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $item->item_name }}</h6>
                                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> Origin: {{ $item->item_location }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning text-dark item-badge-status">
                                                <i class="fa-solid fa-arrow-turn-down me-1"></i> Selected to Return
                                            </span>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        @error('returned_item_ids')
                            <div class="invalid-feedback d-block mt-2">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror

                        <!-- Dynamic Feedback Notice -->
                        <div id="returnStatusNotice" class="alert mt-3 py-2 px-3 small shadow-sm">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Proof of Return Image -->
                    <div class="mb-3">
                        <label for="proof_of_return" class="form-label fw-semibold">
                            Returned Items Photo Proof (Image Proof of Return) <span class="text-muted small fw-normal">(Optional)</span>
                        </label>
                        <input type="file" 
                               name="proof_of_return" 
                               id="proof_of_return" 
                               class="form-control @error('proof_of_return') is-invalid @enderror" 
                               accept="image/*">
                        <small class="text-muted" style="font-size: 11px;">Upload a photo of the returned items verifying their condition.</small>

                        <!-- Image Preview with Close Mark -->
                        <div id="returnProofPreviewContainer" class="position-relative mt-2 d-none p-2 bg-light border rounded text-center" style="max-width: 260px;">
                            <div class="position-relative d-inline-block">
                                <img id="returnProofPreviewImg" src="" alt="Proof Preview" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover;">
                                <button type="button" id="btnRemoveReturnProof" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 26px; height: 26px; padding: 0;" title="Remove image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="small text-muted text-truncate mt-1 px-1 font-monospace" id="returnProofFileName" style="font-size: 11px;"></div>
                        </div>

                        @error('proof_of_return')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Penalty Settlement Checkbox if Penalty Applies -->
                    @if($transaction->penalty_status === 'paid')
                        <div class="p-3 bg-success-subtle border border-success rounded mb-3 d-flex align-items-center justify-content-between shadow-sm">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-success fs-3">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-success mb-1">
                                        Penalty Status: <span class="badge bg-success ms-1"><i class="fa-solid fa-check me-1"></i> PAID</span>
                                    </div>
                                    <small class="text-muted d-block">
                                        Late penalty fee of <strong>₱{{ number_format($transaction->total_penalty, 2) }}</strong> was already settled on <strong>{{ $transaction->penalty_paid_at ? $transaction->penalty_paid_at->format('M d, Y h:i A') : 'Recorded' }}</strong>. No further penalty payment needed.
                                    </small>
                                </div>
                            </div>
                            <span class="badge bg-success py-2 px-3 fs-6 d-none d-sm-inline-block">
                                <i class="fa-solid fa-receipt me-1"></i> SETTLED
                            </span>
                        </div>
                    @elseif($penaltyInfo['amount'] > 0)
                        <div class="form-check p-3 bg-light border rounded mb-3">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="settle_penalty" id="settle_penalty" value="1" {{ old('settle_penalty') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="settle_penalty">
                                Student has paid and settled the ₱{{ number_format($penaltyInfo['amount'], 2) }} penalty now
                            </label>
                            <small class="text-muted d-block ms-4" style="font-size: 11px;">
                                Check this if payment was received in cash to mark the penalty as "PAID". Otherwise, it will remain "UNPAID" and student borrowing remains restricted.
                            </small>
                        </div>
                    @endif

                    <!-- Staff Notes -->
                    <div class="mb-4">
                        <label for="staff_notes" class="form-label fw-semibold">Return Notes / Remarks (Optional)</label>
                        <textarea name="staff_notes" id="staff_notes" class="form-control @error('staff_notes') is-invalid @enderror" rows="2" placeholder="e.g. Returned in good condition, or 1 item missing casing...">{{ old('staff_notes') }}</textarea>
                        @error('staff_notes')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success py-2 px-4 fw-bold" id="btnSubmitReturn">
                            <span id="submitBtnText"><i class="fa-solid fa-check-circle me-1"></i> Confirm & Process Return</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Collect Penalty Modal on Return Page -->
<div class="modal fade" id="collectReturnPenaltyModal" tabindex="-1" aria-labelledby="collectReturnPenaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--ub-maroon);">
                <h5 class="modal-title fw-bold" id="collectReturnPenaltyModalLabel">
                    <i class="fa-solid fa-hand-holding-dollar me-2 text-warning"></i> Collect Equipment Penalty
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="collectReturnPenaltyForm" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Transaction Ref:</span>
                            <span class="font-monospace fw-bold" style="color: var(--ub-maroon);" id="modalReturnRef"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Student:</span>
                            <span class="fw-bold text-dark" id="modalReturnStudent"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-danger">Total Penalty Due:</span>
                            <span class="fs-4 fw-bold text-danger" id="modalReturnAmount">₱0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="returnCashTendered" class="form-label fw-semibold small">Cash Received / Tendered (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">₱</span>
                            <input type="number" step="0.50" min="0" class="form-control form-control-lg fw-bold" id="returnCashTendered" name="amount_received" value="0.00">
                        </div>
                    </div>

                    <div class="p-2 px-3 rounded bg-success-subtle border border-success-subtle mb-3 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold text-success-emphasis">Change / Sukli:</span>
                        <span class="fw-bold fs-5 text-success" id="returnChangeAmount">₱0.00</span>
                    </div>

                    <div class="mb-2">
                        <label for="returnRemarks" class="form-label fw-semibold small">Receipt No. / Remarks (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="returnRemarks" name="remarks" placeholder="e.g. Cash received at counter, OR #">
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
    const totalItems = {{ $totalCount }};
    const alreadyReturned = {{ $returnedCount }};

    function updateReturnStatus() {
        const selectedNow = $('.pending-item-checkbox:checked').length;
        const totalAfterReturn = alreadyReturned + selectedNow;
        const isFullReturn = (totalAfterReturn >= totalItems);

        // Update badge on each row
        $('.pending-item-checkbox').each(function() {
            const badge = $(this).closest('.item-checkbox-label').find('.item-badge-status');
            if ($(this).is(':checked')) {
                badge.removeClass('bg-secondary').addClass('bg-warning text-dark')
                     .html('<i class="fa-solid fa-arrow-turn-down me-1"></i> Selected to Return');
                $(this).closest('.item-checkbox-label').removeClass('opacity-50');
            } else {
                badge.removeClass('bg-warning text-dark').addClass('bg-secondary')
                     .html('<i class="fa-solid fa-minus me-1"></i> Not Returning Now');
                $(this).closest('.item-checkbox-label').addClass('opacity-50');
            }
        });

        if (selectedNow === 0) {
            $('#btnSubmitReturn').prop('disabled', true);
            $('#returnStatusNotice')
                .removeClass('alert-success alert-warning')
                .addClass('alert-secondary')
                .html('<i class="fa-solid fa-circle-info me-1"></i> Please check at least one equipment item above to process a return.');
            $('#submitBtnText').html('<i class="fa-solid fa-hand me-1"></i> Check Items to Return');
        } else if (isFullReturn) {
            $('#btnSubmitReturn').prop('disabled', false);
            $('#returnStatusNotice')
                .removeClass('alert-secondary alert-warning')
                .addClass('alert-success')
                .html('<i class="fa-solid fa-check-double me-1"></i> <strong>Complete Return (' + totalAfterReturn + '/' + totalItems + '):</strong> All equipment items will be returned! This transaction will be officially tagged as <strong class="text-success">RETURNED</strong>.');
            $('#submitBtnText').html('<i class="fa-solid fa-check-double me-1"></i> Complete & Mark as RETURNED (' + totalAfterReturn + '/' + totalItems + ')');
        } else {
            const remaining = totalItems - totalAfterReturn;
            $('#btnSubmitReturn').prop('disabled', false);
            $('#returnStatusNotice')
                .removeClass('alert-secondary alert-success')
                .addClass('alert-warning')
                .html('<i class="fa-solid fa-clock-rotate-left me-1"></i> <strong>Partial Return (' + totalAfterReturn + '/' + totalItems + '):</strong> ' + selectedNow + ' item(s) will be returned now, with <strong>' + remaining + ' item(s) still unreturned</strong>. Status will remain <strong>ONGOING / OVERDUE</strong> and late penalties will continue counting until all items are returned.');
            $('#submitBtnText').html('<i class="fa-solid fa-arrow-rotate-left me-1"></i> Save Partial Return (' + totalAfterReturn + '/' + totalItems + ')');
        }
    }

    $('.pending-item-checkbox').on('change', function() {
        updateReturnStatus();
    });

    $('#btnSelectAllPending').on('click', function() {
        $('.pending-item-checkbox').prop('checked', true);
        updateReturnStatus();
    });

    $('#btnUncheckAllPending').on('click', function() {
        $('.pending-item-checkbox').prop('checked', false);
        updateReturnStatus();
    });

    // Run on page load
    updateReturnStatus();

    // Image Preview with Close Mark
    $('#proof_of_return').on('change', function(e) {
        const file = e.target.files && e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                Toast.fire({
                    icon: 'error',
                    title: 'Please select a valid image file.'
                });
                $(this).val('');
                $('#returnProofPreviewContainer').addClass('d-none');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(evt) {
                $('#returnProofPreviewImg').attr('src', evt.target.result);
                const fileSize = (file.size / 1024).toFixed(1);
                $('#returnProofFileName').text(`${file.name} (${fileSize} KB)`);
                $('#returnProofPreviewContainer').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            $('#returnProofPreviewContainer').addClass('d-none');
            $('#returnProofPreviewImg').attr('src', '');
            $('#returnProofFileName').text('');
        }
    });

    $('#btnRemoveReturnProof').on('click', function(e) {
        e.preventDefault();
        $('#proof_of_return').val('');
        $('#returnProofPreviewImg').attr('src', '');
        $('#returnProofFileName').text('');
        $('#returnProofPreviewContainer').addClass('d-none');
    });

    // Quick Collect Penalty Modal Handler on Return Page
    let currentReturnPenalty = 0;
    function openReturnCollectModal(actionUrl, refNo, studentName, amount) {
        currentReturnPenalty = parseFloat(amount) || 0;
        $('#collectReturnPenaltyForm').attr('action', actionUrl);
        $('#modalReturnRef').text(refNo);
        $('#modalReturnStudent').text(studentName);
        $('#modalReturnAmount').text('₱' + currentReturnPenalty.toFixed(2));
        $('#returnCashTendered').val(currentReturnPenalty.toFixed(2));
        updateReturnChange();
        
        const modal = new bootstrap.Modal(document.getElementById('collectReturnPenaltyModal'));
        modal.show();
    }

    function updateReturnChange() {
        const tendered = parseFloat($('#returnCashTendered').val()) || 0;
        const change = Math.max(0, tendered - currentReturnPenalty);
        $('#returnChangeAmount').text('₱' + change.toFixed(2));
    }

    $('#returnCashTendered').on('input', updateReturnChange);
</script>
@endpush
