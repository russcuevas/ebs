@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--ub-maroon);">Staff Management</h2>
            <p class="text-muted small mb-0">Manage staff handlers across academic departments and laboratories</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-ub-primary">
            <i class="fa-solid fa-user-plus me-1"></i> Add New Staff
        </a>
    </div>

    <div class="card card-ub overflow-hidden">
        <div class="card-header-ub d-flex justify-content-between align-items-center py-3 px-4">
            <div class="d-flex align-items-center gap-2">
                <div
                    style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Staff Handlers Directory</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Staff Member</th>
                            <th>Email Address</th>
                            <th>Department / Office</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($staffMembers) > 0)
                            @foreach ($staffMembers as $staff)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                                                style="width: 34px; height: 34px; font-size: 13px; background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);">
                                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                                            </div>
                                            <div class="fw-bold text-dark text-nowrap" style="font-size: 13.5px;">
                                                {{ $staff->name }}</div>
                                        </div>
                                    </td>
                                    <td class="text-nowrap text-muted" style="font-size: 12.5px;">{{ $staff->email }}</td>
                                    <td class="text-nowrap"><span class="badge bg-light text-dark border px-2 py-1"
                                            style="font-size: 11.5px;">{{ $staff->department ?? 'Unspecified' }}</span></td>

                                    <td class="text-nowrap">
                                        @if ($staff->status === 'active')
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold"
                                                style="font-size: 11.5px;">
                                                <i class="fa-solid fa-circle-check me-1"></i> Active
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold"
                                                style="font-size: 11.5px;">
                                                <i class="fa-solid fa-ban me-1"></i> Blocked
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap"><small
                                            class="text-muted">{{ $staff->created_at->format('M d, Y') }}</small></td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <a href="{{ route('admin.staff.edit', $staff) }}"
                                                class="btn btn-sm btn-light border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; border-radius: 6px;" title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-dark"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border shadow-sm text-danger d-inline-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; border-radius: 6px;" title="Delete"
                                                onclick="confirmDelete('{{ $staff->id }}', '{{ $staff->name }}')">
                                                <i class="fa-solid fa-trash-can text-danger"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $staff->id }}"
                                            action="{{ route('admin.staff.destroy', $staff) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
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

@push('scripts')
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete the staff account for "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7B1113',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
