@extends('layouts.app')

@section('title', 'Borrowing Details - ' . $transaction->reference_no)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Logs
    </a>
    @if($transaction->status !== 'returned')
        <form action="{{ route('admin.borrowings.send-reminder', $transaction) }}" method="POST" onsubmit="return confirm('Send real-time email reminder to the student?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-ub-gold">
                <i class="fa-solid fa-paper-plane me-1"></i> Send Email Reminder Now
            </button>
        </form>
    @endif
</div>

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <div class="card card-ub mb-4">
            <div class="card-header-ub d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-invoice me-2"></i> Transaction: <span class="font-monospace">{{ $transaction->reference_no }}</span></span>
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
                        <small class="text-muted d-block">Student</small>
                        <h5 class="fw-bold mb-0 text-dark">{{ $transaction->student->name ?? 'N/A' }}</h5>
                        <div class="small text-muted">{{ $transaction->student->student_id ?? '' }}</div>
                        <div class="small text-muted">{{ $transaction->student->department ?? '' }}</div>
                        <div class="small font-monospace text-primary">{{ $transaction->student->email ?? '' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Staff / Handler</small>
                        <h6 class="fw-bold mb-0 text-dark">{{ $transaction->staff->name ?? 'N/A' }}</h6>
                        <div class="small text-muted">{{ $transaction->staff->department ?? '' }}</div>
                        <div class="small text-muted">{{ $transaction->staff->email ?? '' }}</div>
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
                                <th>Location / Source</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($transaction->items) > 0)
                                @php $i = 1; @endphp
                                @foreach($transaction->items as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td class="fw-bold">{{ $item->item_name }}</td>
                                    <td><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $item->item_location }}</td>
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
                                <small class="text-muted d-block mt-1">Click the image to open full size.</small>
                            @else
                                <div class="text-muted small py-3 text-center bg-white rounded border">
                                    <i class="fa-regular fa-image fs-3 d-block mb-1 opacity-50"></i>
                                    No photo proof uploaded during borrowing.
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
                                <small class="text-muted d-block mt-1">Click the image to open full size.</small>
                            @else
                                <div class="text-muted small py-3 text-center bg-white rounded border">
                                    <i class="fa-regular fa-image fs-3 d-block mb-1 opacity-50"></i>
                                    Not yet returned or no return photo available.
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

    <!-- Right Column: Timeline, Penalty & Notifications -->
    <div class="col-lg-4">
        <!-- Time & Penalty Card -->
        <div class="card card-ub mb-4">
            <div class="card-header-ub">
                <i class="fa-solid fa-clock me-2"></i> Schedule & Penalty
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <small class="text-muted d-block">Borrowed Date & Time</small>
                    <div class="fw-bold">{{ $transaction->borrow_date_time->format('M d, Y - h:i A') }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Scheduled Due Date & Time</small>
                    <div class="fw-bold {{ $transaction->isOverdue() ? 'text-danger' : 'text-dark' }}">
                        {{ $transaction->due_date_time->format('M d, Y - h:i A') }}
                    </div>
                </div>
                @if($transaction->return_date_time)
                <div class="mb-3">
                    <small class="text-muted d-block">Actual Return Date & Time</small>
                    <div class="fw-bold text-success">{{ $transaction->return_date_time->format('M d, Y - h:i A') }}</div>
                    @if($transaction->returnedByStaff)
                        <small class="text-muted">Received by: {{ $transaction->returnedByStaff->name }}</small>
                    @endif
                </div>
                @endif

                <hr>

                <div class="p-3 rounded {{ $transaction->total_penalty > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-muted' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">Penalty Policy (₱5/day):</span>
                        <span class="badge {{ $transaction->penalty_status === 'paid' ? 'bg-success' : ($transaction->penalty_status === 'waived' ? 'bg-secondary' : 'bg-danger') }}">
                            {{ strtoupper($transaction->penalty_status) }}
                        </span>
                    </div>
                    <div class="fs-4 fw-bold mb-1">
                        ₱{{ number_format($transaction->total_penalty, 2) }}
                    </div>
                    <small style="font-size: 11px;">
                        @if($transaction->penalty_days > 0)
                            {{ $transaction->penalty_days }} day(s) late from scheduled due date.
                        @else
                            No penalty. Returned on time or still within schedule.
                        @endif
                    </small>
                </div>

                @if($transaction->total_penalty > 0 && $transaction->penalty_status === 'unpaid')
                    <div class="mt-3 d-flex gap-2">
                        <form action="{{ route('admin.penalties.settle', $transaction) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="fa-solid fa-check me-1"></i> Mark as Paid
                            </button>
                        </form>
                        <form action="{{ route('admin.penalties.waive', $transaction) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="fa-solid fa-ban me-1"></i> Waive
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notification History -->
        <div class="card card-ub">
            <div class="card-header-ub">
                <i class="fa-solid fa-envelope-circle-check me-2"></i> Sent Email Notifications
            </div>
            <div class="card-body p-3">
                @if(count($transaction->notificationLogs) > 0)
                    <ul class="list-group list-group-flush small">
                        @foreach($transaction->notificationLogs as $log)
                        <li class="list-group-item px-0 py-2">
                            <div class="fw-bold text-dark">
                                @if($log->type === 'due_reminder_1_day')
                                    <i class="fa-solid fa-bell text-warning me-1"></i> 1-Day Before Due Reminder
                                @else
                                    <i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> Overdue Notice
                                @endif
                            </div>
                            <div class="text-muted">{{ $log->recipient_email }}</div>
                            <small class="text-muted" style="font-size: 10px;">{{ $log->sent_at->format('M d, Y h:i A') }}</small>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted small text-center py-3">
                        No automated email notifications recorded for this transaction.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
