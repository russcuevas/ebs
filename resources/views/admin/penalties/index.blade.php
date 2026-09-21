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

<div class="card card-ub overflow-hidden">
    <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Penalty Records List</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100 mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Ref No.</th>
                        <th>Student</th>
                        <th>Due Date</th>
                        <th>Date Returned</th>
                        <th>Days Late</th>
                        <th>Total Penalty</th>
                        <th>Penalty Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($penalties) > 0)
                        @foreach($penalties as $trans)
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <a href="{{ route('admin.borrowings.show', $trans) }}"
                                    class="text-decoration-none fw-bold font-monospace d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded"
                                    style="color: var(--ub-maroon); background: #fff1f2; border: 1px solid #ffe4e6; font-size: 12px;">
                                    <i class="fa-solid fa-barcode text-muted" style="font-size: 11px;"></i>
                                    <span>{{ $trans->reference_no }}</span>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                                        style="width: 34px; height: 34px; font-size: 13px; background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);">
                                        {{ strtoupper(substr($trans->student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-nowrap" style="font-size: 13.5px;">{{ $trans->student->name ?? 'N/A' }}</div>
                                        <div class="d-flex align-items-center gap-1.5 text-muted small text-nowrap" style="font-size: 11.5px;">
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5 fw-semibold">{{ $trans->student->student_id ?? 'N/A' }}</span>
                                            <span class="text-truncate" style="max-width: 140px;" title="{{ $trans->student->department ?? '' }}">{{ $trans->student->department ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $trans->due_date_time->format('M d, Y') }}</div>
                                <div class="text-muted small" style="font-size: 11.5px;">
                                    <i class="fa-regular fa-clock me-1 opacity-75"></i>{{ $trans->due_date_time->format('h:i A') }}
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @if($trans->return_date_time)
                                    <div class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $trans->return_date_time->format('M d, Y') }}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">
                                        <i class="fa-regular fa-clock me-1 opacity-75"></i>{{ $trans->return_date_time->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">Not yet returned</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold" style="font-size: 12px;">
                                    {{ $trans->penalty_days }} day(s)
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <span class="fw-bold fs-6 text-danger">₱{{ number_format($trans->total_penalty, 2) }}</span>
                            </td>
                            <td class="text-nowrap">
                                @if($trans->penalty_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-check me-1"></i> Paid
                                    </span>
                                @elseif($trans->penalty_status === 'waived')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-ban me-1"></i> Waived
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                @if($trans->penalty_status === 'unpaid')
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <form action="{{ route('admin.penalties.settle', $trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm receipt of ₱{{ $trans->total_penalty }} penalty payment?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1 px-2.5 py-1" style="font-size: 11.5px;" title="Mark as Paid">
                                                <i class="fa-solid fa-check"></i> Paid
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.penalties.waive', $trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to waive this penalty?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 11.5px;" title="Waive">
                                                <i class="fa-solid fa-ban"></i> Waive
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <small class="text-muted"><i class="fa-solid fa-circle-check text-success me-1"></i> Settled</small>
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
