@extends('layouts.app')

@section('title', 'Log In')

@section('content')
    <!-- Left Side: Edge-to-Edge Full Campus Image Showcase -->
    <div class="auth-split-left">
        <div class="auth-split-left-overlay"></div>

        <!-- Top Left Floating University Campus Badge -->
        <div class="auth-split-left-content">
            <div class="auth-hero-pill">
                <i class="fa-solid fa-graduation-cap text-warning"></i>
                <span>University of Batangas <br> Lipa City Campus</span>
            </div>
        </div>

        <!-- Bottom Floating Caption -->
        <div class="auth-split-left-content">
            <h2 class="fw-bolder text-white mb-2" style="font-size: 2.35rem; letter-spacing: -0.8px; line-height: 1.12;">
                Empowering Brahman<br>
                <span style="color: var(--ub-gold);">Excellence.</span>
            </h2>
            <p class="text-white-50 small mb-0" style="max-width: 440px;">
                Equipment Borrowing System &bull; Fast, digital, and secure campus asset management.
            </p>
        </div>
    </div>

    <!-- Right Side: Clean Form Panel matching UB Reference -->
    <div class="auth-split-right">
        <div class="auth-form-panel">
            <!-- Top Small UB Seal Icon & Subtitle -->
            <div class="text-center mb-4">
                <div class="ub-seal-icon-small">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 1.5px; font-size: 11px;">
                    UNIVERSITY OF BATANGAS LIPA CAMPUS
                </div>
                <h3 class="fw-bold mt-2 mb-0" style="color: #1e293b; font-size: 22px; letter-spacing: -0.5px;">
                    Log into EBS
                </h3>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show small py-2 px-3 mb-3 border-0 shadow-sm"
                    role="alert">
                    <i class="fa-solid fa-circle-check me-1 text-success"></i> {{ session('status') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show small py-2 px-3 mb-3 border-0 shadow-sm"
                    role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1 text-danger"></i> {{ session('error') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Email Field -->
                <div class="mb-3">
                    <input type="email" name="email" id="email"
                        class="form-control auth-input-clean @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field with Clean Toggle -->
                <div class="mb-3">
                    <div class="input-group">
                        <input type="password" name="password" id="password"
                            class="form-control auth-input-clean @error('password') is-invalid @enderror"
                            style="border-top-right-radius: 0; border-bottom-right-radius: 0;" placeholder="Password"
                            required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn-clean" id="togglePasswordBtn"
                            title="Toggle visibility" tabindex="-1">
                            <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Primary Maroon Button: Log in -->
                <button type="submit" class="btn btn-auth-primary-clean mb-2">
                    Log in
                </button>

                <!-- Secondary Text Link -->
                <div class="text-center my-2">
                    <span class="small text-muted">Exclusively for UB students and personnel</span>
                </div>

                <!-- Outline Maroon Button: Create new account -->
                <a href="{{ route('register') }}" class="btn btn-auth-outline-clean">
                    Create new account
                </a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
@endpush
