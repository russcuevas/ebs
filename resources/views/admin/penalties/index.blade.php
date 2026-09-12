@extends('layouts.app')

@section('title', 'Penalties Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Penalties Monitoring</h2>
        <p class="text-muted small mb-0">Track and manage overdue fines for late equipment returns (₱5.00/day)</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-ub border-start border-4 border-danger p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-danger small fw-semibold">Total Unpaid Penalties</div>
                    <h3 class="fw-bold mb-0 text-danger">₱{{ number_format($totalUnpaid, 2) }}</h3>
                </div>
                <div class="fs-1 text-danger opacity-50"><i class="fa-solid fa-sack-xmark"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-ub border-start border-4 border-success p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-success small fw-semibold">Total Collected / Paid Penalties</div>
                    <h3 class="fw-bold mb-0 text-success">₱{{ number_format($totalCollected, 2) }}</h3>
                </div>
                <div class="fs-1 text-success opacity-50"><i class="fa-solid fa-sack-dollar"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card card-ub">
    <div class="card-header-ub">
        <i class="fa-solid fa-receipt me-2"></i> Penalty Records List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Student</th>
                        <th>Due Date</th>
                        <th>Date Returned</th>
                        <th>Days Late</th>
                        <th>Total Penalty</th>
                        <th>Penalty Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($penalties) > 0)
                        @foreach($penalties as $trans)
                        <tr>
                            <td class="font-monospace fw-bold">
                                <a href="{{ route('admin.borrowings.show', $trans) }}" style="color: var(--ub-maroon);" class="text-decoration-none">
                                    {{ $trans->reference_no }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $trans->student->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $trans->student->student_id ?? '' }} &bull; {{ $trans->student->department ?? '' }}</small>
                            </td>
                            <td><small>{{ $trans->due_date_time->format('M d, Y h:i A') }}</small></td>
                            <td>
                                <small>
                                    {{ $trans->return_date_time ? $trans->return_date_time->format('M d, Y h:i A') : 'Not yet returned' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $trans->penalty_days }} day(s)</span>
                            </td>
                            <td>
                                <span class="fw-bold fs-6 text-danger">₱{{ number_format($trans->total_penalty, 2) }}</span>
                            </td>
                            <td>
                                @if($trans->penalty_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($trans->penalty_status === 'waived')
                                    <span class="badge bg-secondary">Waived</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($trans->penalty_status === 'unpaid')
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.penalties.settle', $trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm receipt of ₱{{ $trans->total_penalty }} penalty payment?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Mark as Paid">
                                                <i class="fa-solid fa-check"></i> Paid
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.penalties.waive', $trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to waive this penalty?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Waive">
                                                <i class="fa-solid fa-ban"></i> Waive
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <small class="text-muted">Settled</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
