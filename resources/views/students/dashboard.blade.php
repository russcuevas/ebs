@extends('layouts.app')

@section('title', 'Student Dashboard & QR Code')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Student Dashboard</h2>
        <p class="text-muted small mb-0">Welcome, <strong>{{ $student->name }}</strong>! Here is your official Student QR Code and borrowing status.</p>
    </div>
    <a href="{{ route('student.history') }}" class="btn btn-ub-primary">
        <i class="fa-solid fa-clock-rotate-left me-1"></i> My Borrowing History
    </a>
</div>

<!-- Warning if Student is Blocked -->
@if($isBlocked)
    <div class="alert alert-danger border-2 border-danger shadow-sm mb-4 p-3">
        <div class="d-flex align-items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation fs-2 text-danger flex-shrink-0"></i>
            <div>
                <h5 class="fw-bold text-danger mb-1">NOTICE: BORROWING IS CURRENTLY RESTRICTED</h5>
                <p class="small mb-1">
                    You have overdue equipment or outstanding unpaid penalties in the system.
                </p>
                @if($totalUnpaidPenalties > 0)
                    <div class="fw-bold fs-6 text-danger">
                        Outstanding Unpaid Penalties: ₱{{ number_format($totalUnpaidPenalties, 2) }}
                    </div>
                @endif
                <small class="text-dark d-block mt-1">
                    Please contact the designated equipment handler to return items and settle any penalties.
                </small>
            </div>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Left Column: Student Official QR Code Card -->
    <div class="col-lg-5">
        <div class="card card-ub text-center p-4 h-100 shadow-sm">
            <div class="mb-2">
                <span class="badge bg-warning text-dark px-3 py-1 fw-bold rounded-pill text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                    Official UB Student QR
                </span>
            </div>

            <div class="mb-3">
                <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="rounded-circle border border-2 border-warning shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
            </div>

            <div class="p-3 bg-white border rounded-3 d-inline-block mx-auto mb-3 shadow-sm" style="max-width: 260px;">
                <!-- QR Code SVG Render -->
                <div class="qr-svg-container" style="width: 220px; height: 220px; margin: 0 auto;">
                    {!! $qrCodeSvg !!}
                </div>
            </div>

            <h5 class="fw-bold mb-1 text-dark">{{ $student->name }}</h5>
            <div class="font-monospace text-primary fw-semibold mb-1">{{ $student->student_id }}</div>
            <div class="small text-muted mb-2">{{ $student->department }}</div>
            <div class="small text-muted font-monospace mb-3">{{ $student->email }}</div>

            <div class="alert alert-light border small text-muted py-2 px-3 mb-3 text-start">
                <i class="fa-solid fa-circle-info text-primary me-1"></i> Present this QR Code to the staff handler whenever borrowing university equipment.
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm" id="downloadQrBtn" onclick="downloadQrImage()" title="Download QR Image">
                <i class="fa-solid fa-download me-1"></i> Print / Save QR
            </button>
        </div>
    </div>

    <!-- Right Column: Policy Explanation & Quick Statistics -->
    <div class="col-lg-7">
        <!-- Transparent Penalty Policy Info Box -->
        <div class="card card-ub border-start border-4 border-warning mb-4 shadow-sm">
            <div class="card-header-ub bg-white">
                <i class="fa-solid fa-scale-balanced me-2 text-warning"></i> Due Date & Penalty Policy
            </div>
            <div class="card-body p-4">
                <h6 class="fw-bold" style="color: var(--ub-maroon);">How the ₱5.00/day Penalty Works:</h6>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    Every borrowed equipment has a scheduled <strong>Due Date and Time</strong>. Fines begin accumulating on the <strong>day after the due date</strong>:
                </p>

                <div class="p-3 bg-light rounded border mb-3">
                    <div class="row g-2 align-items-center small">
                        <div class="col-12 col-sm-4">
                            <span class="badge bg-secondary p-2 d-block text-center">Example: Sept 9 Due</span>
                        </div>
                        <div class="col-12 col-sm-8 text-muted">
                            <div>&bull; <strong>Sept 9:</strong> Due date (No fine if returned within schedule).</div>
                            <div>&bull; <strong>Sept 10:</strong> 1 day late &rarr; <strong class="text-danger">₱5.00 fine</strong>.</div>
                            <div>&bull; <strong>Sept 11+:</strong> Daily accruing penalty &rarr; <strong class="text-danger">₱10.00, ₱15.00...</strong></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning py-2 px-3 small mb-0">
                    <i class="fa-solid fa-bell me-1"></i> <strong>Automated Email:</strong> You will receive an email reminder on your UB Gmail <strong>1 day before the due date</strong>.
                </div>
            </div>
        </div>

        <!-- Metric Statistics Grid -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card card-ub p-3 text-center h-100">
                    <span class="text-muted small">Ongoing</span>
                    <h3 class="fw-bold my-1 text-warning">{{ $totalOngoing }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Active borrowed</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-ub p-3 text-center h-100">
                    <span class="text-danger small">Overdue Items</span>
                    <h3 class="fw-bold my-1 text-danger">{{ $totalOverdue }}</h3>
                    <small class="text-danger" style="font-size: 11px;">Past due date</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-ub p-3 text-center h-100">
                    <span class="text-muted small">Returned</span>
                    <h3 class="fw-bold my-1 text-success">{{ $totalReturned }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Completed</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-ub p-3 text-center h-100">
                    <span class="text-danger small">Unpaid Penalties</span>
                    <h3 class="fw-bold my-1 text-danger">₱{{ number_format($totalUnpaidPenalties, 2) }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Total fine</small>
                </div>
            </div>
        </div>

        <!-- Active Borrowings Card -->
        <div class="card card-ub shadow-sm">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-hand-holding-box me-2"></i> Currently Borrowed Equipment</span>
                <a href="{{ route('student.history', ['status' => 'ongoing']) }}" class="btn btn-sm btn-link p-0 text-decoration-none" style="color: var(--ub-maroon);">View All</a>
            </div>
            <div class="card-body p-3">
                @if(count($activeBorrowings) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($activeBorrowings as $trans)
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="font-monospace fw-bold" style="color: var(--ub-maroon);">{{ $trans->reference_no }}</span>
                                    <span class="badge {{ $trans->isOverdue() ? 'bg-danger' : 'bg-warning text-dark' }} ms-2">
                                        {{ $trans->isOverdue() ? 'OVERDUE' : 'ONGOING' }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Due Date:</small>
                                    <span class="small fw-bold {{ $trans->isOverdue() ? 'text-danger' : 'text-dark' }}">
                                        {{ $trans->due_date_time->format('M d, Y h:i A') }}
                                    </span>
                                </div>
                            </div>
                            <div class="small mb-2">
                                <strong>Items:</strong>
                                @foreach($trans->items as $item)
                                    <span class="badge bg-light text-dark border me-1">{{ $item->item_name }} ({{ $item->item_location }})</span>
                                @endforeach
                            </div>
                            @if($trans->total_penalty > 0)
                                <div class="small text-danger fw-bold">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Current Penalty: ₱{{ number_format($trans->total_penalty, 2) }} ({{ $trans->penalty_days }} day(s) late)
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fa-regular fa-circle-check fs-2 text-success mb-2 d-block"></i>
                        <p class="mb-0">You currently have no borrowed equipment. Present your Student QR code to the staff handler when borrowing.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function downloadQrImage() {
    const container = document.querySelector('.qr-svg-container');
    if (!container) return;
    const svgElement = container.querySelector('svg');
    if (!svgElement) return;

    // Clone SVG to ensure we can manipulate size without altering the on-screen card
    const clonedSvg = svgElement.cloneNode(true);
    const canvasSize = 800; // High resolution for clear scanning
    clonedSvg.setAttribute('width', canvasSize);
    clonedSvg.setAttribute('height', canvasSize);

    if (!clonedSvg.getAttribute('xmlns')) {
        clonedSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    }

    const svgString = new XMLSerializer().serializeToString(clonedSvg);
    const svgDataUrl = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgString);

    const img = new Image();
    img.onload = function () {
        const canvas = document.createElement('canvas');
        canvas.width = canvasSize;
        canvas.height = canvasSize;
        const ctx = canvas.getContext('2d');

        // Clean white background for contrast and scanner compatibility
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvasSize, canvasSize);

        // Draw the QR Code
        ctx.drawImage(img, 0, 0, canvasSize, canvasSize);

        const filename = '{{ \Illuminate\Support\Str::slug($student->student_id ?: $student->name) }}-qr-code.png';

        try {
            canvas.toBlob(function(blob) {
                if (blob) {
                    const blobUrl = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
                } else {
                    fallbackDataUrlDownload(canvas, filename);
                }
            }, 'image/png');
        } catch (e) {
            fallbackDataUrlDownload(canvas, filename);
        }

        if (typeof Toast !== 'undefined') {
            Toast.fire({
                icon: 'success',
                title: 'QR Code image downloaded successfully!'
            });
        }
    };

    img.onerror = function () {
        // Fallback: download as SVG image directly if canvas fails
        const fallbackBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
        const fallbackUrl = URL.createObjectURL(fallbackBlob);
        const link = document.createElement('a');
        link.href = fallbackUrl;
        link.download = '{{ \Illuminate\Support\Str::slug($student->student_id ?: $student->name) }}-qr-code.svg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(() => URL.revokeObjectURL(fallbackUrl), 1000);
    };

    img.src = svgDataUrl;
}

function fallbackDataUrlDownload(canvas, filename) {
    const dataUrl = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = dataUrl;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush

