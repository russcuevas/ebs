@extends('layouts.app')

@section('title', 'Add New Staff')

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
                <i class="fa-solid fa-user-plus me-2"></i> Create New Staff Account
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.staff.store') }}" method="POST" novalidate>
                    @csrf

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="e.g. Maria Santos" 
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
                               value="{{ old('email') }}" 
                               placeholder="e.g. msantos@ub.edu.ph" 
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
                               value="{{ old('department') }}" 
                               placeholder="e.g. College of Computer Studies / Library / Science Lab" 
                               required>
                        @error('department')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimum 6 characters" 
                               required>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-ub-primary">
                            <i class="fa-solid fa-save me-1"></i> Save Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
