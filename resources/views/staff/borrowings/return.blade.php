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
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-arrow-rotate-left me-2"></i> Equipment Return: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
                <span class="badge bg-warning text-dark">Currently Borrowed</span>
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

                <!-- Borrowed Items List -->
                <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-boxes-stacked me-1"></i> Returning Equipment Items:</h6>
                <ul class="list-group mb-4 small">
                    @foreach($transaction->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-cube text-primary me-2"></i>
                                <strong>{{ $item->item_name }}</strong>
                                <span class="text-muted ms-2">({{ $item->item_location }})</span>
                            </div>
                            <span class="badge bg-secondary">To Return</span>
                        </li>
                    @endforeach
                </ul>

                <!-- Penalty Box if Overdue -->
                @if($penaltyInfo['amount'] > 0)
                    <div class="alert alert-danger border-2 border-danger p-3 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation fs-2 text-danger flex-shrink-0"></i>
                            <div>
                                <h5 class="fw-bold text-danger mb-1">OVERDUE TRANSACTION (PENALTY DUE)</h5>
                                <p class="small mb-1">
                                    Equipment return is late by <strong>{{ $penaltyInfo['days'] }} day(s)</strong> from the scheduled due date.
                                </p>
                                <div class="fs-4 fw-bold text-danger">
                                    Penalty: ₱{{ number_format($penaltyInfo['amount'], 2) }}
                                    <small class="fs-6 text-muted fw-normal">(₱5.00 per day)</small>
                                </div>
                                <small class="text-dark d-block mt-2">
                                    Note: Future borrowing permissions remain locked until all penalties are settled.
                                </small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success py-2 px-3 small mb-4">
                        <i class="fa-solid fa-circle-check me-1"></i> <strong>On Time:</strong> Returned within the scheduled deadline. No penalty applies.
                    </div>
                @endif

                <!-- Return Processing Form -->
                <form action="{{ route('staff.borrowings.process-return', $transaction) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Proof of Return Image -->
                    <div class="mb-3">
                        <label for="proof_of_return" class="form-label fw-semibold">
                            Returned Items Photo Proof (Image Proof of Return) <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               name="proof_of_return" 
                               id="proof_of_return" 
                               class="form-control @error('proof_of_return') is-invalid @enderror" 
                               accept="image/*" 
                               required>
                        <small class="text-muted" style="font-size: 11px;">Upload a clear photo of the equipment verifying its safe return.</small>
                        @error('proof_of_return')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Penalty Settlement Checkbox if Penalty Applies -->
                    @if($penaltyInfo['amount'] > 0)
                        <div class="form-check p-3 bg-light border rounded mb-3">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="settle_penalty" id="settle_penalty" value="1" {{ old('settle_penalty') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="settle_penalty">
                                Student has paid and settled the ₱{{ number_format($penaltyInfo['amount'], 2) }} penalty now
                            </label>
                            <small class="text-muted d-block ms-4" style="font-size: 11px;">
                                Check this if payment was received in cash to mark the penalty as "PAID" and unblock the student. Otherwise, it will remain "UNPAID".
                            </small>
                        </div>
                    @endif

                    <!-- Staff Notes -->
                    <div class="mb-4">
                        <label for="staff_notes" class="form-label fw-semibold">Return Notes / Remarks (Optional)</label>
                        <textarea name="staff_notes" id="staff_notes" class="form-control @error('staff_notes') is-invalid @enderror" rows="2" placeholder="e.g. Items returned in complete and working condition...">{{ old('staff_notes') }}</textarea>
                        @error('staff_notes')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success py-2 px-4 fw-bold">
                            <i class="fa-solid fa-check-circle me-1"></i> Confirm & Complete Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
