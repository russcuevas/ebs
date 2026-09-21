@extends('layouts.app')

@section('title', 'Scan Student QR')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);"><i class="fa-solid fa-qrcode me-2"></i> Student QR Scanner</h2>
        <p class="text-muted small mb-0">Scan student QR code or enter Student ID to verify eligibility and initiate borrowing</p>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Camera Scanner & Manual Input -->
    <div class="col-lg-6">
        <div class="card card-ub shadow-sm mb-4">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-camera me-2"></i> Live Camera Scanner</span>
                <span class="badge bg-secondary" id="scannerStatus">Ready</span>
            </div>
            <div class="card-body p-3 text-center">
                <!-- Camera Viewfinder Box -->
                <div id="reader" style="width: 100%; min-height: 280px; background: #000; border-radius: 8px; overflow: hidden;" class="mb-3"></div>
                
                <div class="d-flex justify-content-center gap-2 mb-2">
                    <button type="button" class="btn btn-sm btn-ub-primary" id="btnStartScan">
                        <i class="fa-solid fa-video me-1"></i> Start Camera
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnStopScan">
                        <i class="fa-solid fa-video-slash me-1"></i> Stop Camera
                    </button>
                </div>
                <small class="text-muted" style="font-size: 11px;">Point the camera towards the student's official QR code on their screen or ID.</small>
            </div>
        </div>

        <!-- Manual Lookup Fallback -->
        <div class="card card-ub shadow-sm">
            <div class="card-header-ub">
                <i class="fa-solid fa-keyboard me-2"></i> Manual Search (Fallback)
            </div>
            <div class="card-body p-3">
                <form id="manualLookupForm" onsubmit="event.preventDefault(); performLookup($('#manualInput').val());">
                    <div class="input-group">
                        <input type="text" id="manualInput" class="form-control" placeholder="Enter Student ID, QR Token, or Email..." autofocus>
                        <button type="submit" class="btn btn-ub-primary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size: 11px;">Example: <code>UB-2024-00123</code> or <code>student@ub.edu.ph</code></small>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Student Result Card & Borrow Permission -->
    <div class="col-lg-6">
        <!-- Placeholder / Waiting state -->
        <div id="placeholderState" class="card card-ub p-5 text-center text-muted">
            <i class="fa-solid fa-qrcode fs-1 opacity-50 mb-3" style="color: var(--ub-maroon);"></i>
            <h5 class="fw-bold">No Student Scanned</h5>
            <p class="small mb-0">Scan the QR code on the left or type into the search box to check student details and verify borrowing eligibility.</p>
        </div>

        <!-- Loading spinner -->
        <div id="loadingState" class="card card-ub p-5 text-center d-none">
            <div class="spinner-border text-danger mx-auto mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="fw-bold text-dark">Looking up student record...</h6>
        </div>

        <!-- Student Found Details Container -->
        <div id="studentDetailsCard" class="card card-ub shadow-sm d-none">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-id-card me-2"></i> Student Information</span>
                <span id="verifiedBadge" class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="fa-solid fa-check-circle me-1"></i> Verified UB Student
                </span>
            </div>
            <div class="card-body p-4">
                <!-- Overdue / Penalty Red Warning Box -->
                <div id="blockedWarningBox" class="alert alert-danger border-2 border-danger d-none mb-4 p-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-ban fs-2 text-danger flex-shrink-0"></i>
                        <div>
                            <h5 class="fw-bold text-danger mb-1">BORROWING RESTRICTED (BLOCKED)</h5>
                            <p class="small mb-2" id="blockedReasonText">
                                The student currently has overdue equipment or unpaid penalties in the system.
                            </p>
                            <div class="fw-bold fs-6 text-danger" id="unpaidPenaltyAmountText"></div>
                            <div class="small mt-2 fst-italic">
                                Overdue equipment must be returned and penalties settled before borrowing again.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear / Eligible Green Box -->
                <div id="eligibleSuccessBox" class="alert alert-success border-2 border-success d-none mb-4 p-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-circle-check fs-2 text-success flex-shrink-0"></i>
                        <div>
                            <h6 class="fw-bold text-success mb-1">ELIGIBLE TO BORROW</h6>
                            <p class="small mb-0">This student has no overdue equipment and zero unpaid penalties.</p>
                        </div>
                    </div>
                </div>

                <!-- Student Profile Details with Selfie Avatar -->
                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                    <img id="studentAvatarImg" src="" alt="Student Selfie" class="rounded-circle border border-2 border-warning shadow-sm flex-shrink-0" style="width: 68px; height: 68px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <small class="text-muted d-block" style="font-size: 11px;">Verified Student Record</small>
                        <h5 class="fw-bold mb-0 text-dark" id="studentName"></h5>
                        <div class="small text-primary font-monospace fw-semibold" id="studentIdNum"></div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">College / Department</small>
                        <div class="fw-semibold text-dark" id="studentDept"></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Official Email (@ub.edu.ph)</small>
                        <div class="small text-muted font-monospace" id="studentEmail"></div>
                    </div>
                </div>

                <!-- Active Borrowings of Student if any -->
                <div id="activeBorrowingsListSection" class="mb-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold small text-muted text-uppercase mb-0">
                            <i class="fa-solid fa-boxes-stacked me-1 text-danger"></i> Currently Borrowed Equipment & Status
                        </h6>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold" id="activeBorrowingsCountBadge"></span>
                    </div>
                    <div class="d-flex flex-column gap-3" id="activeBorrowingsContainer">
                        <!-- Dynamically injected via JS -->
                    </div>
                </div>

                <!-- Action Button -->
                <div id="actionButtonsContainer">
                    <a href="#" id="btnProceedBorrow" class="btn btn-ub-primary w-100 py-2 fs-6">
                        <i class="fa-solid fa-plus-circle me-1"></i> Proceed to Record Borrowing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- HTML5-QRCode Scanner Library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrCode = null;

    function initScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                // QR code successfully decoded
                $('#scannerStatus').text('Detected!').removeClass('bg-secondary').addClass('bg-success');
                performLookup(decodedText);
            },
            (errorMessage) => {
                // scanning...
            }
        ).then(() => {
            $('#btnStartScan').addClass('d-none');
            $('#btnStopScan').removeClass('d-none');
            $('#scannerStatus').text('Camera Active').removeClass('bg-secondary').addClass('bg-success');
        }).catch((err) => {
            Toast.fire({
                icon: 'warning',
                title: 'Unable to access camera: ' + err
            });
            $('#scannerStatus').text('No Camera');
        });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                $('#btnStartScan').removeClass('d-none');
                $('#btnStopScan').addClass('d-none');
                $('#scannerStatus').text('Ready').removeClass('bg-success').addClass('bg-secondary');
            });
        }
    }

    $('#btnStartScan').on('click', function() {
        initScanner();
    });

    $('#btnStopScan').on('click', function() {
        stopScanner();
    });

    function performLookup(queryCode) {
        if (!queryCode || !queryCode.trim()) {
            Toast.fire({
                icon: 'info',
                title: 'Please enter a student QR code or ID.'
            });
            return;
        }

        $('#placeholderState').addClass('d-none');
        $('#studentDetailsCard').addClass('d-none');
        $('#loadingState').removeClass('d-none');

        $.ajax({
            url: "{{ route('staff.scanner.lookup') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                code: queryCode.trim()
            },
            success: function(response) {
                $('#loadingState').addClass('d-none');

                if (response.success && response.student) {
                    displayStudent(response);
                } else {
                    $('#placeholderState').removeClass('d-none');
                    Toast.fire({
                        icon: 'error',
                        title: response.message || 'Student not found.'
                    });
                }
            },
            error: function(xhr) {
                $('#loadingState').addClass('d-none');
                $('#placeholderState').removeClass('d-none');

                let errorMsg = 'Student not found.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            }
        });
    }

    function displayStudent(data) {
        const student = data.student;
        const isBlocked = data.is_blocked;
        const unpaidPenalties = data.unpaid_penalties;
        const reasons = data.block_reasons || [];
        const activeBorrowings = data.active_borrowings || [];

        $('#studentName').text(student.name);
        $('#studentIdNum').text(student.student_id || 'No ID');
        $('#studentDept').text(student.department || 'No Department');
        $('#studentEmail').text(student.email);
        $('#studentAvatarImg').attr('src', student.avatar_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.name) + '&background=7B1113&color=F5B800');

        if (isBlocked) {
            $('#blockedWarningBox').removeClass('d-none');
            $('#eligibleSuccessBox').addClass('d-none');

            let reasonHtml = reasons.join('<br>');
            $('#blockedReasonText').html(reasonHtml || 'Has overdue equipment or unpaid penalties.');

            if (unpaidPenalties > 0) {
                $('#unpaidPenaltyAmountText').text('Total Unpaid Penalties: ₱' + parseFloat(unpaidPenalties).toFixed(2));
            } else {
                $('#unpaidPenaltyAmountText').text('');
            }

            // Disable or lock button
            $('#btnProceedBorrow')
                .addClass('btn-secondary disabled')
                .removeClass('btn-ub-primary')
                .attr('href', '#')
                .html('<i class="fa-solid fa-lock me-1"></i> Borrowing Locked (Overdue / Penalties)');
        } else {
            $('#blockedWarningBox').addClass('d-none');
            $('#eligibleSuccessBox').removeClass('d-none');

            const borrowUrl = "{{ route('staff.borrowings.create') }}?student_id=" + student.id;
            $('#btnProceedBorrow')
                .removeClass('btn-secondary disabled')
                .addClass('btn-ub-primary')
                .attr('href', borrowUrl)
                .html('<i class="fa-solid fa-plus-circle me-1"></i> Record Borrowing for ' + student.name);
        }

        // Render active borrowings list with item-by-item breakdown
        if (activeBorrowings.length > 0) {
            $('#activeBorrowingsListSection').removeClass('d-none');
            $('#activeBorrowingsCountBadge').text(activeBorrowings.length + (activeBorrowings.length > 1 ? ' Active Borrowings' : ' Active Borrowing'));

            let html = '';
            activeBorrowings.forEach(function(trans) {
                const isOverdue = trans.is_overdue || trans.status === 'overdue';
                const statusBadge = isOverdue
                    ? `<span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> OVERDUE</span>`
                    : `<span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-clock me-1"></i> ONGOING</span>`;

                let itemsHtml = '';
                if (trans.items && trans.items.length > 0) {
                    trans.items.forEach(function(item) {
                        if (item.is_returned) {
                            itemsHtml += `
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border border-light-subtle mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold" style="font-size: 11px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Nabalik Na (Returned)
                                        </span>
                                        <span class="text-decoration-line-through text-muted fw-semibold">${item.name}</span>
                                        ${item.location ? `<small class="text-muted">(${item.location})</small>` : ''}
                                    </div>
                                    <span class="text-success small fw-bold"><i class="fa-solid fa-check"></i></span>
                                </div>`;
                        } else {
                            itemsHtml += `
                                <div class="d-flex align-items-center justify-content-between p-2 rounded ${isOverdue ? 'bg-danger-subtle bg-opacity-25 border border-danger' : 'bg-warning-subtle bg-opacity-25 border border-warning'} mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge ${isOverdue ? 'bg-danger text-white' : 'bg-warning text-dark'} px-2 py-1 fw-bold" style="font-size: 11px;">
                                            <i class="fa-solid fa-hand-holding-box me-1"></i> DI PA NABABALIK
                                        </span>
                                        <strong class="text-dark">${item.name}</strong>
                                        ${item.location ? `<span class="text-muted small">(${item.location})</span>` : ''}
                                    </div>
                                    <span class="badge bg-white text-danger border shadow-sm small fw-semibold">Pending Return</span>
                                </div>`;
                        }
                    });
                } else {
                    itemsHtml = `<div class="text-muted small p-2">No individual item details recorded.</div>`;
                }

                html += `
                <div class="card border ${isOverdue ? 'border-danger' : 'border-warning'} shadow-sm">
                    <div class="card-header ${isOverdue ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-dark'} d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="font-monospace fw-bold px-2 py-0.5 bg-white rounded border" style="font-size: 12px; color: var(--ub-maroon);">
                                <i class="fa-solid fa-barcode text-muted me-1"></i>${trans.reference_no}
                            </span>
                            <span class="small fw-semibold">
                                Due: <strong class="${isOverdue ? 'text-danger' : 'text-dark'}">${trans.due_date_formatted || 'N/A'}</strong>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            ${statusBadge}
                            <a href="${trans.return_url}" class="btn btn-sm btn-success fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 11.5px;" title="Process Return Now">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Process Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 11px;">
                            <i class="fa-solid fa-list-check me-1"></i> Items Status Breakdown:
                        </div>
                        <div class="items-list-wrapper">
                            ${itemsHtml}
                        </div>
                        ${trans.penalty_amount > 0 ? `
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center text-danger small fw-bold">
                                <span><i class="fa-solid fa-sack-xmark me-1"></i> Running Late Penalty:</span>
                                <span>₱${parseFloat(trans.penalty_amount).toFixed(2)} (${(trans.penalty_status || 'unpaid').toUpperCase()})</span>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
            });
            $('#activeBorrowingsContainer').html(html);
        } else {
            $('#activeBorrowingsListSection').addClass('d-none');
        }

        $('#studentDetailsCard').removeClass('d-none');
    }
</script>
@endpush
