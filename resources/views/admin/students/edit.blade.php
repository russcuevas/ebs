@extends('layouts.app')

@section('title', 'Edit Student - ' . $student->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Student Directory
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-ub shadow-sm">
            <div class="card-header-ub d-flex align-items-center justify-content-between py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; background: rgba(123, 17, 19, 0.08); color: var(--ub-maroon); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <span class="fw-bold fs-6" style="color: var(--ub-maroon);">Edit Student: {{ $student->name }}</span>
                </div>
                <span class="badge bg-light text-dark border font-monospace">{{ $student->student_id }}</span>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.students.update', $student) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $student->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Student ID -->
                    <div class="mb-3">
                        <label for="student_id" class="form-label fw-semibold">Student ID Number <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="student_id" 
                               id="student_id" 
                               class="form-control @error('student_id') is-invalid @enderror" 
                               value="{{ old('student_id', $student->student_id) }}" 
                               required>
                        @error('student_id')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div class="mb-3">
                        <label for="department" class="form-label fw-semibold">College / Department <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="department" 
                               id="department" 
                               class="form-control @error('department') is-invalid @enderror" 
                               value="{{ old('department', $student->department) }}" 
                               placeholder="e.g. College of Computer Studies, College of Engineering"
                               required>
                        @error('department')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Official UB Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', preg_replace('/@ub\.edu\.ph$/i', '', $student->email)) }}" 
                                   required>
                            <span class="input-group-text bg-light text-muted small fw-bold border-start-0 font-monospace">
                                @ub.edu.ph
                            </span>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phone_number" class="form-label fw-semibold">Mobile Phone (Optional)</label>
                        <input type="tel" 
                               name="phone_number" 
                               id="phone_number" 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number', $student->phone_number) }}">
                        @error('phone_number')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Account Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active (Can borrow items)</option>
                            <option value="blocked" {{ old('status', $student->status) === 'blocked' ? 'selected' : '' }}>Blocked (Cannot borrow items)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Verification Checkbox -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="is_verified" {{ old('is_verified', $student->email_verified_at ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_verified">
                                Email Verified via OTP
                            </label>
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">Unchecking this will require the student to verify their email before logging in.</small>
                    </div>

                    <!-- Password (Optional on edit) -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Reset Password (Optional)</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Leave blank to keep existing password">
                        <small class="text-muted" style="font-size: 11px;">Only enter if you wish to set a new password for this student (min. 8 chars).</small>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between gap-2 pt-2 border-top">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-ub-primary px-4">
                            <i class="fa-solid fa-check me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
