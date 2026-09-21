@extends('layouts.app')

@section('title', 'Verify Email OTP')

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
                Check your official UB Gmail to complete registration.
            </p>
        </div>
    </div>

    <!-- Right Side: OTP Form Panel -->
    <div class="auth-split-right">
        <div class="auth-form-panel text-center">
            <!-- Top Small UB Seal Icon & Subtitle -->
            <div class="mb-3">
                <div class="ub-seal-icon-small">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 1.5px; font-size: 11px;">
                    UNIVERSITY OF BATANGAS LIPA CAMPUS
                </div>
                <h3 class="fw-bold mt-1 mb-2" style="color: #1e293b; font-size: 22px; letter-spacing: -0.5px;">
                    Check Your UB Gmail
                </h3>
                <p class="text-muted small mb-2">
                    A 6-digit confirmation code was sent to:
                </p>
                <div class="badge bg-light text-dark border px-3 py-2 font-monospace mb-3" style="font-size: 13px;">
                    <i class="fa-solid fa-envelope text-primary me-1"></i> {{ $user->email }}
                </div>
            </div>

            @if (session('dev_otp_fallback'))
                <div class="alert alert-warning text-start small py-2 px-3 mb-3 border-0 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> {{ session('dev_otp_fallback') }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success text-start small py-2 px-3 mb-3 border-0 shadow-sm">
                    <i class="fa-solid fa-circle-check me-1 text-success"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <input type="text" name="otp" id="otp"
                        class="form-control text-center fw-bold fs-2 font-monospace auth-input-clean @error('otp') is-invalid @enderror"
                        maxlength="6" placeholder="------" value="{{ old('otp') }}"
                        style="letter-spacing: 12px; height: 56px; border-radius: 8px;" required autofocus
                        autocomplete="one-time-code">
                    @error('otp')
                        <div class="invalid-feedback text-center mt-2">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Primary Maroon Button -->
                <button type="submit" class="btn btn-auth-primary-clean mb-3">
                    Verify & Access Student QR
                </button>
            </form>

            <!-- Resend and Logout Actions -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <form method="POST" action="{{ route('verification.resend') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 small text-decoration-none fw-semibold"
                        style="color: #7B1113;">
                        <i class="fa-solid fa-rotate-right me-1"></i> Resend Code
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 small text-muted text-decoration-none">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
