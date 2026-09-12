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
                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Currently Borrowed Equipment:</h6>
                    <ul class="list-group list-group-flush border rounded small" id="activeBorrowingsList">
                        <!-- injected via JS -->
                    </ul>
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

        // Render active borrowings list
        if (activeBorrowings.length > 0) {
            $('#activeBorrowingsListSection').removeClass('d-none');
            let listHtml = '';
            activeBorrowings.forEach(function(trans) {
                const isOverdue = trans.status === 'overdue';
                listHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="font-monospace">${trans.reference_no}</strong>
                        <small class="text-muted d-block">Due: ${trans.due_date_time}</small>
                    </div>
                    <span class="badge ${isOverdue ? 'bg-danger' : 'bg-warning text-dark'}">${trans.status.toUpperCase()}</span>
                </li>`;
            });
            $('#activeBorrowingsList').html(listHtml);
        } else {
            $('#activeBorrowingsListSection').addClass('d-none');
        }

        $('#studentDetailsCard').removeClass('d-none');
    }
</script>
@endpush
