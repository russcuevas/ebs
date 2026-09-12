@extends('layouts.app')

@section('title', 'Record Borrowing')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Record Equipment Borrowing</h2>
        <p class="text-muted small mb-0">Input borrowed items for the verified student</p>
    </div>
    <a href="{{ route('staff.scanner.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-qrcode me-1"></i> Scan Student First
    </a>
</div>

<form action="{{ route('staff.borrowings.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="row g-4">
        <!-- Left Column: Student Selection & Schedule -->
        <div class="col-lg-5">
            <!-- Student Information Card -->
            <div class="card card-ub mb-4">
                <div class="card-header-ub d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-user-graduate me-2"></i> Borrowing Student</span>
                    <a href="{{ route('staff.scanner.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none" style="color: var(--ub-maroon);">
                        <i class="fa-solid fa-camera me-1"></i> Scan QR
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($selectedStudent)
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                        
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border mb-3">
                            <div style="width: 48px; height: 48px; background: var(--ub-gold-light); color: var(--ub-maroon); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700;">
                                {{ strtoupper(substr($selectedStudent->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $selectedStudent->name }}</h6>
                                <div class="small text-muted font-monospace">{{ $selectedStudent->student_id }}</div>
                                <div class="small text-muted">{{ $selectedStudent->department }}</div>
                                <div class="small text-muted">{{ $selectedStudent->email }}</div>
                            </div>
                        </div>

                        <!-- Check if student is blocked -->
                        @if($selectedStudent->hasOverdueOrUnpaidPenalties())
                            <div class="alert alert-danger p-3 mb-0">
                                <h6 class="fw-bold text-danger mb-1"><i class="fa-solid fa-ban me-1"></i> BORROWING RESTRICTED!</h6>
                                <p class="small mb-1">The student has overdue equipment or unpaid penalties (₱{{ number_format($selectedStudent->totalUnpaidPenalties(), 2) }}).</p>
                                <small class="fw-bold">Items must be returned and penalties settled before borrowing.</small>
                            </div>
                        @else
                            <div class="alert alert-success py-2 px-3 small mb-0">
                                <i class="fa-solid fa-circle-check me-1"></i> <strong>Eligible:</strong> Student has no overdue items or penalties.
                            </div>
                        @endif
                    @else
                        <!-- If student not yet scanned, allow quick dropdown or scanner link -->
                        <div class="mb-3">
                            <label for="student_select" class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_select" class="form-select @error('student_id') is-invalid @enderror" onchange="if(this.value) window.location.href='{{ route('staff.borrowings.create') }}?student_id=' + this.value">
                                <option value="">-- Select Student or Scan QR --</option>
                                @php
                                    $allStudents = \App\Models\User::where('role', 'student')->whereNotNull('email_verified_at')->orderBy('name')->get();
                                @endphp
                                @foreach($allStudents as $st)
                                    <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }} ({{ $st->student_id }} - {{ $st->department }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 11px;">Recommended: <a href="{{ route('staff.scanner.index') }}" class="fw-bold text-danger">Scan the student's QR code</a> for instant lookup.</small>
                        </div>
                    @endif

                    @error('student_id')
                        <div class="invalid-feedback d-block mt-2">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Date & Time Card -->
            <div class="card card-ub mb-4">
                <div class="card-header-ub">
                    <i class="fa-solid fa-calendar-days me-2"></i> Borrowing & Return Schedule
                </div>
                <div class="card-body p-3">
                    <!-- Borrow Date & Time -->
                    <div class="mb-3">
                        <label for="time_date_to_borrow" class="form-label fw-semibold">Borrow Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" 
                               name="time_date_to_borrow" 
                               id="time_date_to_borrow" 
                               class="form-control @error('time_date_to_borrow') is-invalid @enderror" 
                               value="{{ old('time_date_to_borrow', date('Y-m-d\TH:i')) }}" 
                               required>
                        @error('time_date_to_borrow')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Return Date & Time -->
                    <div class="mb-3">
                        <label for="time_date_to_returned" class="form-label fw-semibold">Scheduled Due Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" 
                               name="time_date_to_returned" 
                               id="time_date_to_returned" 
                               class="form-control @error('time_date_to_returned') is-invalid @enderror" 
                               value="{{ old('time_date_to_returned', date('Y-m-d\T17:00')) }}" 
                               required>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">
                            <strong>Notice:</strong> Fines of ₱5.00 per day begin after this scheduled date and time.
                        </small>
                        @error('time_date_to_returned')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Proof of Borrowing (Optional Image) -->
                    <div class="mb-3">
                        <label for="proof_of_borrowing" class="form-label fw-semibold">Proof Photo of Items (Optional)</label>
                        <input type="file" 
                               name="proof_of_borrowing" 
                               id="proof_of_borrowing" 
                               class="form-control @error('proof_of_borrowing') is-invalid @enderror" 
                               accept="image/*">
                        <small class="text-muted" style="font-size: 11px;">You may take a photo of the equipment before releasing.</small>
                        @error('proof_of_borrowing')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Staff Notes -->
                    <div class="mb-2">
                        <label for="staff_notes" class="form-label fw-semibold">Staff Notes (Optional)</label>
                        <textarea name="staff_notes" id="staff_notes" class="form-control @error('staff_notes') is-invalid @enderror" rows="2" placeholder="e.g. Minor scratch on casing...">{{ old('staff_notes') }}</textarea>
                        @error('staff_notes')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Dynamic Multiple Items Table -->
        <div class="col-lg-7">
            <div class="card card-ub shadow-sm">
                <div class="card-header-ub d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-boxes-stacked me-2"></i> Equipment Items List (Multiple Items)</span>
                    <button type="button" class="btn btn-sm btn-ub-gold" id="btnAddItemRow">
                        <i class="fa-solid fa-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="card-body p-3">
                    <p class="small text-muted mb-3">
                        Multiple items can be borrowed in a single transaction. Click "Add Item" to include more items.
                    </p>

                    @error('items')
                        <div class="alert alert-danger py-2 px-3 small mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Item Name <span class="text-danger">*</span></th>
                                    <th style="width: 45%;">Origin / Location <span class="text-danger">*</span></th>
                                    <th style="width: 10%; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @php
                                    $oldItems = old('items');
                                @endphp

                                @if($oldItems && is_array($oldItems) && count($oldItems) > 0)
                                    @foreach($oldItems as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" 
                                                   name="items[{{ $index }}][name]" 
                                                   class="form-control form-control-sm @error("items.{$index}.name") is-invalid @enderror" 
                                                   placeholder="e.g. HDMI Cable, Projector Remote" 
                                                   value="{{ $item['name'] ?? '' }}" 
                                                   required>
                                            @error("items.{$index}.name")
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="items[{{ $index }}][location]" 
                                                   class="form-control form-control-sm @error("items.{$index}.location") is-invalid @enderror" 
                                                   placeholder="e.g. Lab 3, AV Room, Gym" 
                                                   value="{{ $item['location'] ?? '' }}" 
                                                   required>
                                            @error("items.{$index}.location")
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <!-- Default Initial Row -->
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" 
                                                   name="items[0][name]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="e.g. HDMI Cable, Projector Remote, Microphone" 
                                                   required>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="items[0][location]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="e.g. Lab 3, CCS AV Room, Gym" 
                                                   required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-ub-primary py-2 px-4" @if($selectedStudent && $selectedStudent->hasOverdueOrUnpaidPenalties()) disabled @endif>
                            <i class="fa-solid fa-check me-1"></i> Submit & Record Borrowing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let rowIndex = {{ $oldItems ? count($oldItems) : 1 }};

    $('#btnAddItemRow').on('click', function() {
        const rowHtml = `
            <tr class="item-row">
                <td>
                    <input type="text" 
                           name="items[${rowIndex}][name]" 
                           class="form-control form-control-sm" 
                           placeholder="e.g. HDMI Cable, Projector Remote" 
                           required>
                </td>
                <td>
                    <input type="text" 
                           name="items[${rowIndex}][location]" 
                           class="form-control form-control-sm" 
                           placeholder="e.g. Lab 3, AV Room" 
                           required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#itemsTableBody').append(rowHtml);
        rowIndex++;
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#itemsTableBody .item-row').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Toast.fire({
                icon: 'warning',
                title: 'At least one item is required.'
            });
        }
    });
</script>
@endpush
