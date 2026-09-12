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

<div class="card card-ub">
    <div class="card-header-ub">
        <i class="fa-solid fa-users-gear me-2"></i> Staff Handlers Directory
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light">
                    <tr>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Department / Office</th>
                        <th>Recorded Transactions</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($staffMembers) > 0)
                        @foreach($staffMembers as $staff)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 36px; height: 36px; background: var(--ub-gold-light); color: var(--ub-maroon); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold">{{ $staff->name }}</div>
                                </div>
                            </td>
                            <td>{{ $staff->email }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $staff->department ?? 'Unspecified' }}</span></td>
                            <td>
                                <span class="badge bg-secondary">{{ $staff->staff_borrowings_count }} transaction(s)</span>
                            </td>
                            <td>
                                @if($staff->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Blocked</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $staff->created_at->format('M d, Y') }}</small></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete('{{ $staff->id }}', '{{ $staff->name }}')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $staff->id }}" action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="d-none">
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
