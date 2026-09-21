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
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('staff.scanner.index') }}"
                                class="btn btn-sm btn-link p-0 text-decoration-none" style="color: var(--ub-maroon);">
                                <i class="fa-solid fa-camera me-1"></i> Scan QR
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if ($selectedStudent)
                            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

                            <div class="position-relative p-3 bg-light rounded border mb-3">
                                <!-- Quick X / Deselect Button -->
                                <a href="{{ route('staff.borrowings.create') }}"
                                    class="btn btn-sm btn-light border text-danger position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 28px; height: 28px; padding: 0; transition: all 0.2s ease;"
                                    title="Remove / Deselect this student">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>

                                <div class="d-flex align-items-center gap-3 pe-4">
                                    @if ($selectedStudent->avatar_url)
                                        <img src="{{ $selectedStudent->avatar_url }}" alt="{{ $selectedStudent->name }}"
                                            class="rounded-circle border border-2 border-warning shadow-sm flex-shrink-0"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <div style="width: 48px; height: 48px; background: var(--ub-gold-light); color: var(--ub-maroon); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700;"
                                            class="flex-shrink-0">
                                            {{ strtoupper(substr($selectedStudent->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $selectedStudent->name }}</h6>
                                        <div class="small text-muted font-monospace">{{ $selectedStudent->student_id }}
                                        </div>
                                        <div class="small text-muted">{{ $selectedStudent->department }}</div>
                                        <div class="small text-muted">{{ $selectedStudent->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Check if student is blocked -->
                            @if ($selectedStudent->hasOverdueOrUnpaidPenalties())
                                <div class="alert alert-danger p-3 mb-3">
                                    <h6 class="fw-bold text-danger mb-1"><i class="fa-solid fa-ban me-1"></i> BORROWING
                                        RESTRICTED!</h6>
                                    <p class="small mb-1">The student has overdue equipment or unpaid penalties
                                        (₱{{ number_format($selectedStudent->totalUnpaidPenalties(), 2) }}).</p>
                                    <small class="fw-bold d-block mb-2">Items must be returned and penalties settled before
                                        borrowing.</small>
                                    @if($selectedStudent->totalUnpaidPenalties() > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-success fw-bold" 
                                                onclick="openCollectStudentModal('{{ route('staff.students.settle-penalties', $selectedStudent) }}', '{{ addslashes($selectedStudent->name) }}', {{ (float) $selectedStudent->totalUnpaidPenalties() }})">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Collect & Settle Penalty (₱{{ number_format($selectedStudent->totalUnpaidPenalties(), 2) }})
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-success py-2 px-3 small mb-3">
                                    <i class="fa-solid fa-circle-check me-1"></i> <strong>Eligible:</strong> Student has no
                                    overdue items or penalties.
                                </div>
                            @endif

                            <!-- Dropdown to directly switch to another student -->
                            <div id="changeStudentDropdownSection" class="p-3 bg-light rounded border mb-2">
                                <label for="change_student_select" class="form-label small fw-semibold text-dark mb-1 d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-arrows-rotate me-1 text-warning"></i> Switch / Search Another Student:</span>
                                    <a href="{{ route('staff.borrowings.create') }}" class="text-danger small text-decoration-none fw-normal">
                                        <i class="fa-solid fa-xmark me-1"></i> Deselect
                                    </a>
                                </label>
                                <select id="change_student_select" class="form-select select2-student" style="width: 100%;">
                                    <option value="">-- Type Student ID or Name to Switch --</option>
                                    @foreach ($allStudents as $st)
                                        <option value="{{ $st->id }}"
                                            data-student-id="{{ $st->student_id }}"
                                            data-name="{{ $st->name }}"
                                            data-department="{{ $st->department }}"
                                            data-email="{{ $st->email }}"
                                            data-avatar="{{ $st->avatar_url }}"
                                            {{ $st->id == $selectedStudent->id ? 'selected' : '' }}>
                                            {{ $st->student_id }} — {{ $st->name }} ({{ $st->department }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <!-- If student not yet scanned, allow searchable Select2 dropdown or scanner link -->
                            <div class="mb-3">
                                <label for="student_select" class="form-label fw-semibold">
                                    <i class="fa-solid fa-magnifying-glass me-1 text-danger"></i> Search & Select Student <span class="text-danger">*</span>
                                </label>
                                <select name="student_id" id="student_select"
                                    class="form-select select2-student @error('student_id') is-invalid @enderror" style="width: 100%;">
                                    <option value="">-- Search Student ID (e.g. UB-2024-00123) or Name --</option>
                                    @foreach ($allStudents as $st)
                                        <option value="{{ $st->id }}"
                                            data-student-id="{{ $st->student_id }}"
                                            data-name="{{ $st->name }}"
                                            data-department="{{ $st->department }}"
                                            data-email="{{ $st->email }}"
                                            data-avatar="{{ $st->avatar_url }}"
                                            {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                            {{ $st->student_id }} — {{ $st->name }} ({{ $st->department }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted" style="font-size: 11px;">
                                        <i class="fa-solid fa-keyboard me-1"></i> Type <strong>Student ID</strong> (e.g. <code>UB-2024-00123</code>) or name to filter.
                                    </small>
                                    <a href="{{ route('staff.scanner.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" style="color: var(--ub-maroon); font-size: 11.5px;">
                                        <i class="fa-solid fa-qrcode me-1"></i> Scan QR Camera
                                    </a>
                                </div>
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
                            <label for="time_date_to_borrow" class="form-label fw-semibold">Borrow Date & Time <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="time_date_to_borrow" id="time_date_to_borrow"
                                class="form-control @error('time_date_to_borrow') is-invalid @enderror"
                                value="{{ old('time_date_to_borrow', date('Y-m-d\TH:i')) }}" required>
                            @error('time_date_to_borrow')
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Return Date & Time -->
                        <div class="mb-3">
                            <label for="time_date_to_returned" class="form-label fw-semibold">Scheduled Due Date & Time
                                <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="time_date_to_returned" id="time_date_to_returned"
                                class="form-control @error('time_date_to_returned') is-invalid @enderror"
                                value="{{ old('time_date_to_returned', date('Y-m-d\T17:00')) }}" required>
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
                            <label for="proof_of_borrowing" class="form-label fw-semibold">Proof Photo of Items
                                (Optional)</label>
                            <input type="file" name="proof_of_borrowing" id="proof_of_borrowing"
                                class="form-control @error('proof_of_borrowing') is-invalid @enderror" accept="image/*">
                            <small class="text-muted" style="font-size: 11px;">You may take a photo of the equipment
                                before releasing.</small>

                            <!-- Image Preview with Close Mark -->
                            <div id="proofPreviewContainer" class="position-relative mt-2 d-none p-2 bg-light border rounded text-center" style="max-width: 260px;">
                                <div class="position-relative d-inline-block">
                                    <img id="proofPreviewImg" src="" alt="Proof Preview" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover;">
                                    <button type="button" id="btnRemoveProof" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 26px; height: 26px; padding: 0;" title="Remove image">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <div class="small text-muted text-truncate mt-1 px-1 font-monospace" id="proofFileName" style="font-size: 11px;"></div>
                            </div>

                            @error('proof_of_borrowing')
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Staff Notes -->
                        <div class="mb-2">
                            <label for="staff_notes" class="form-label fw-semibold">Staff Notes (Optional)</label>
                            <textarea name="staff_notes" id="staff_notes" class="form-control @error('staff_notes') is-invalid @enderror"
                                rows="2" placeholder="e.g. Minor scratch on casing...">{{ old('staff_notes') }}</textarea>
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

                                    @if ($oldItems && is_array($oldItems) && count($oldItems) > 0)
                                        @foreach ($oldItems as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <input type="text" name="items[{{ $index }}][name]"
                                                        class="form-control form-control-sm @error("items.{$index}.name") is-invalid @enderror"
                                                        placeholder="e.g. HDMI Cable, Projector Remote"
                                                        value="{{ $item['name'] ?? '' }}" required>
                                                    @error("items.{$index}.name")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" name="items[{{ $index }}][location]"
                                                        class="form-control form-control-sm @error("items.{$index}.location") is-invalid @enderror"
                                                        placeholder="e.g. Lab 3, AV Room, Gym"
                                                        value="{{ $item['location'] ?? '' }}" required>
                                                    @error("items.{$index}.location")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-remove-row"
                                                        title="Remove">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <!-- Default Initial Row -->
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" name="items[0][name]"
                                                    class="form-control form-control-sm"
                                                    placeholder="e.g. HDMI Cable, Projector Remote, Microphone" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][location]"
                                                    class="form-control form-control-sm"
                                                    placeholder="e.g. Lab 3, CCS AV Room, Gym" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove">
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
                            <button type="submit" class="btn btn-ub-primary py-2 px-4"
                                @if ($selectedStudent && $selectedStudent->hasOverdueOrUnpaidPenalties()) disabled @endif>
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

        // Image Preview with Close/Remove Mark
        $('#proof_of_borrowing').on('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please select a valid image file.'
                    });
                    $(this).val('');
                    $('#proofPreviewContainer').addClass('d-none');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#proofPreviewImg').attr('src', evt.target.result);
                    const fileSize = (file.size / 1024).toFixed(1);
                    $('#proofFileName').text(`${file.name} (${fileSize} KB)`);
                    $('#proofPreviewContainer').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                $('#proofPreviewContainer').addClass('d-none');
                $('#proofPreviewImg').attr('src', '');
                $('#proofFileName').text('');
            }
        });

        $('#btnRemoveProof').on('click', function(e) {
            e.preventDefault();
            $('#proof_of_borrowing').val('');
            $('#proofPreviewImg').attr('src', '');
            $('#proofFileName').text('');
            $('#proofPreviewContainer').addClass('d-none');
        });

        let currentStudentPenalty = 0;
        function openCollectStudentModal(actionUrl, studentName, penaltyAmount) {
            currentStudentPenalty = parseFloat(penaltyAmount) || 0;
            $('#collectStudentPenaltyForm').attr('action', actionUrl);
            $('#modalStudentNameText').text(studentName);
            $('#modalStudentPenaltyDue').text('₱' + currentStudentPenalty.toFixed(2));
            $('#modalStudentCashTendered').val(currentStudentPenalty.toFixed(2));
            $('#modalStudentChangeAmount').text('₱0.00');
            $('#modalStudentRemarks').val('');

            const modal = new bootstrap.Modal(document.getElementById('collectStudentPenaltyModal'));
            modal.show();
        }

        $('#modalStudentCashTendered').on('input', function() {
            const tendered = parseFloat($(this).val()) || 0;
            const change = Math.max(0, tendered - currentStudentPenalty);
            $('#modalStudentChangeAmount').text('₱' + change.toFixed(2));
        });

        // Initialize Select2 on Student Dropdown with search by ID, name, dept
        function formatStudentOption(state) {
            if (!state.id) {
                return state.text;
            }
            const el = $(state.element);
            const studentId = el.data('student-id') || '';
            const dept = el.data('department') || '';
            const name = el.data('name') || state.text;
            const avatar = el.data('avatar');

            const avatarHtml = avatar 
                ? `<img src="${avatar}" class="rounded-circle border me-2.5 flex-shrink-0" style="width: 32px; height: 32px; object-fit: cover;">`
                : `<span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold me-2.5 flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px; background: #fff1f2; color: var(--ub-maroon); border: 1px solid #ffe4e6;">${name.charAt(0).toUpperCase()}</span>`;

            return $(`
                <div class="d-flex align-items-center justify-content-between py-1 px-1">
                    <div class="d-flex align-items-center">
                        ${avatarHtml}
                        <div>
                            <div class="fw-bold" style="font-size: 13.5px; line-height: 1.2; color: #0f172a;">${name}</div>
                            <div class="small mt-0.5" style="font-size: 11.5px; color: #64748b;">
                                <span class="badge bg-white text-dark border me-1 font-monospace" style="font-size: 11px;">${studentId}</span>
                                <span>${dept}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        function formatStudentSelection(state) {
            if (!state.id) {
                return state.text;
            }
            const el = $(state.element);
            const studentId = el.data('student-id') || '';
            const name = el.data('name') || state.text;
            return studentId ? `${studentId} — ${name}` : name;
        }

        $('.select2-student').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Search by Student ID (e.g. UB-2024-00123) or Name --',
            allowClear: true,
            width: '100%',
            templateResult: formatStudentOption,
            templateSelection: formatStudentSelection,
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();
                const el = $(data.element);
                const studentId = (el.data('student-id') || '').toString().toLowerCase();
                const dept = (el.data('department') || '').toString().toLowerCase();
                const email = (el.data('email') || '').toString().toLowerCase();
                const name = (el.data('name') || '').toString().toLowerCase();

                if (text.indexOf(term) > -1 || studentId.indexOf(term) > -1 || dept.indexOf(term) > -1 || email.indexOf(term) > -1 || name.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        }).on('select2:select', function(e) {
            const selectedId = e.params.data.id;
            if (selectedId) {
                window.location.href = "{{ route('staff.borrowings.create') }}?student_id=" + selectedId;
            }
        });
    </script>
@endpush

<!-- Collect Student Penalty Modal -->
<div class="modal fade" id="collectStudentPenaltyModal" tabindex="-1" aria-labelledby="collectStudentPenaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--ub-maroon);">
                <h5 class="modal-title fw-bold" id="collectStudentPenaltyModalLabel">
                    <i class="fa-solid fa-hand-holding-dollar me-2 text-warning"></i> Collect Student Penalty Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="collectStudentPenaltyForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Student:</span>
                            <span class="fw-bold text-dark" id="modalStudentNameText"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-danger">Total Outstanding Fine:</span>
                            <span class="fs-4 fw-bold text-danger" id="modalStudentPenaltyDue">₱0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modalStudentCashTendered" class="form-label fw-semibold small">Cash Received / Tendered (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">₱</span>
                            <input type="number" step="0.50" min="0" class="form-control form-control-lg fw-bold" id="modalStudentCashTendered" name="amount_received" placeholder="0.00">
                        </div>
                    </div>

                    <div class="p-2 px-3 rounded bg-success-subtle border border-success-subtle mb-3 d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold text-success-emphasis">Change / Sukli:</span>
                        <span class="fw-bold fs-5 text-success" id="modalStudentChangeAmount">₱0.00</span>
                    </div>

                    <div class="mb-2">
                        <label for="modalStudentRemarks" class="form-label fw-semibold small">Receipt No. / Remarks (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="modalStudentRemarks" name="remarks" placeholder="e.g. Counter Cash Receipt #">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-check-circle me-1"></i> Confirm & Settle Fine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
