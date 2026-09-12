@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-ub shadow-sm">
            <div class="card-header-ub">
                <i class="fa-solid fa-user-pen me-2"></i> Edit Staff Account: {{ $staff->name }}
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.staff.update', $staff) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $staff->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $staff->email) }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div class="mb-3">
                        <label for="department" class="form-label fw-semibold">Department / Office <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="department" 
                               id="department" 
                               class="form-control @error('department') is-invalid @enderror" 
                               value="{{ old('department', $staff->department) }}" 
                               required>
                        @error('department')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Account Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', $staff->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="blocked" {{ old('status', $staff->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password (Optional on edit) -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">New Password (Optional)</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Leave blank to keep existing password">
                        <small class="text-muted" style="font-size: 11px;">Only enter if you wish to change the password.</small>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-ub-primary">
                            <i class="fa-solid fa-save me-1"></i> Update Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
