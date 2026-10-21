@extends('layouts.app')

@section('title', 'Borrower Registration (Student / Teacher)')

@push('styles')
    <style>
        /* Multi-step indicator */
        .step-stepper-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .step-stepper-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step-stepper-item.active {
            color: var(--ub-maroon);
        }

        .step-stepper-item.completed {
            color: #10b981;
        }

        .step-stepper-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            transition: all 0.25s ease;
        }

        .step-stepper-item.active .step-stepper-circle {
            border-color: var(--ub-maroon);
            background: var(--ub-maroon);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(123, 17, 19, 0.25);
        }

        .step-stepper-item.completed .step-stepper-circle {
            border-color: #10b981;
            background: #10b981;
            color: #ffffff;
        }

        .step-stepper-line {
            width: 32px;
            height: 2px;
            background: #e2e8f0;
            transition: background 0.25s ease;
        }

        .step-stepper-line.completed {
            background: #10b981;
        }

        /* Camera Viewport */
        .camera-frame-wrapper {
            position: relative;
            width: 240px;
            height: 240px;
            margin: 0 auto 16px;
            border-radius: 50%;
            overflow: hidden;
            background: #0f172a;
            border: 3.5px solid var(--ub-maroon);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .camera-frame-wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 16px 36px rgba(123, 17, 19, 0.22);
        }

        .camera-frame-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .camera-frame-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-placeholder-guide {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            pointer-events: none;
            background: radial-gradient(circle, rgba(123, 17, 19, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
        }

        .face-oval-guide {
            position: absolute;
            width: 72%;
            height: 82%;
            border: 2.5px dashed rgba(245, 184, 0, 0.85);
            border-radius: 50%;
            pointer-events: none;
        }

        @media (max-width: 576px) {
            .camera-frame-wrapper {
                width: 200px;
                height: 200px;
            }

            .step-stepper-nav {
                gap: 8px;
            }

            .step-stepper-line {
                width: 20px;
            }
        }

        /* ================= SUBMISSION LOADING OVERLAY ================= */
        .auth-loading-overlay {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            -webkit-user-select: none;
            cursor: wait;
        }

        .auth-loading-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .auth-loading-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 38px 30px;
            max-width: 430px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15);
            position: relative;
            transform: scale(0.88);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: default;
        }

        .auth-loading-overlay.active .auth-loading-card {
            transform: scale(1);
        }

        .auth-loading-spinner-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-loading-ring-outer {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3.5px solid rgba(123, 17, 19, 0.14);
            border-top-color: var(--ub-maroon, #7B1113);
            border-right-color: var(--ub-gold, #F5B800);
            animation: authSpinnerRotate 1.1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
        }

        .auth-loading-ring-inner {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(123, 17, 19, 0.12) 0%, rgba(245, 184, 0, 0.08) 100%);
            animation: authPulseGlow 2s ease-in-out infinite;
        }

        .auth-loading-icon-center {
            position: relative;
            z-index: 2;
            font-size: 26px;
            color: var(--ub-maroon, #7B1113);
            animation: authPulseGlow 2s ease-in-out infinite;
        }

        .auth-loading-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.4px;
        }

        .auth-loading-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 18px;
            line-height: 1.45;
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .auth-loading-progress-track {
            height: 6px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
            margin: 0 0 18px;
            position: relative;
        }

        .auth-loading-progress-bar {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 45%;
            background: linear-gradient(90deg, var(--ub-maroon, #7B1113), var(--ub-gold, #F5B800), var(--ub-maroon, #7B1113));
            background-size: 200% 100%;
            border-radius: 999px;
            animation: authShimmerSlide 1.8s ease-in-out infinite;
        }

        .auth-loading-steps {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
            margin-bottom: 18px;
        }

        .auth-loading-step-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            transition: all 0.3s ease;
        }

        .auth-loading-step-item.active {
            color: var(--ub-maroon, #7B1113);
            font-weight: 700;
        }

        .auth-loading-step-item.completed {
            color: #10b981;
        }

        .auth-loading-step-item i {
            width: 18px;
            text-align: center;
            font-size: 13px;
        }

        .auth-loading-footer {
            font-size: 11.5px;
            color: #94a3b8;
            line-height: 1.4;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        @keyframes authSpinnerRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes authPulseGlow {
            0%, 100% { transform: scale(0.95); opacity: 0.85; }
            50% { transform: scale(1.06); opacity: 1; }
        }

        @keyframes authShimmerSlide {
            0% { left: -45%; }
            100% { left: 100%; }
        }

        @media (max-width: 576px) {
            .auth-loading-card {
                padding: 28px 20px;
                border-radius: 18px;
            }
            .auth-loading-title {
                font-size: 18px;
            }
        }
    </style>
@endpush

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
                Equipment Borrowing Pass &bull; Verified selfie photo & instant digital QR registration for Students & Faculty.
            </p>
        </div>
    </div>

    <!-- Right Side: Registration Form Panel with Multi-step -->
    <div class="auth-split-right">
        <div class="auth-form-panel" style="max-width: 440px;">
            <!-- Top Small UB Seal Icon & Subtitle -->
            <div class="text-center mb-3">
                <div class="ub-seal-icon-small">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="text-uppercase fw-bold small text-muted" style="letter-spacing: 1.5px; font-size: 11px;">
                    UNIVERSITY OF BATANGAS LIPA CAMPUS
                </div>
                <h3 class="fw-bold mt-1 mb-0" style="color: #1e293b; font-size: 22px; letter-spacing: -0.5px;">
                    Borrower Registration
                </h3>
                <div class="text-muted" style="font-size: 12px;">Student &amp; Faculty Equipment Pass</div>
            </div>

            <!-- 2-Step Stepper Header -->
            <div class="step-stepper-nav">
                <div class="step-stepper-item active" id="stepperNav1">
                    <div class="step-stepper-circle" id="stepperCircle1">1</div>
                    <span>Face Selfie</span>
                </div>
                <div class="step-stepper-line" id="stepperLine"></div>
                <div class="step-stepper-item" id="stepperNav2">
                    <div class="step-stepper-circle" id="stepperCircle2">2</div>
                    <span>Details</span>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show small py-2 px-3 mb-3 border-0 shadow-sm"
                    role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1 text-danger"></i> {{ session('error') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->has('profile_photo'))
                <div class="alert alert-danger small py-2 px-3 mb-3 border-0 shadow-sm">
                    <i class="fa-solid fa-camera me-1"></i> {{ $errors->first('profile_photo') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="multiStepRegisterForm" enctype="multipart/form-data"
                novalidate>
                @csrf

                <!-- Hidden field to hold base64 image data -->
                <input type="hidden" name="profile_photo_data" id="profilePhotoData"
                    value="{{ old('profile_photo_data') }}">

                <!-- Native Camera Shutter input (Works on ALL mobile devices, iPhones & Androids without HTTPS restrictions) -->
                <input type="file" id="nativeCameraInput" accept="image/*" capture="user" style="display: none;">

                <!-- ================= STEP 1: FACE VERIFICATION / SELFIE ================= -->
                <div id="stepSection1">
                    <div class="text-center mb-3">
                        <p class="small text-muted mb-0">
                            Please take a clear front selfie photo for your borrower profile.
                        </p>
                    </div>

                    <!-- Camera / Photo Viewport (Clickable to snap selfie) -->
                    <div class="camera-frame-wrapper" id="cameraFrame" title="Tap to take selfie">
                        <!-- Live Video Stream (if desktop webcam is enabled) -->
                        <video id="webcamVideo" autoplay playsinline muted style="display: none;"></video>

                        <!-- Captured Image Preview -->
                        <img id="photoPreviewImg" src="{{ old('profile_photo_data') ?? '' }}" alt="Student Selfie"
                            style="{{ old('profile_photo_data') ? 'display: block;' : 'display: none;' }}">

                        <!-- Default Placeholder before Photo Taken -->
                        <div class="camera-placeholder-guide" id="cameraPlaceholder"
                            style="{{ old('profile_photo_data') ? 'display: none;' : '' }}">
                            <i class="fa-solid fa-camera-retro fs-1 mb-2 text-warning"></i>
                            <span class="small fw-bold">Tap to Take Selfie</span>
                            <span style="font-size: 11px; opacity: 0.85;">Front camera capture required</span>
                        </div>

                        <!-- Face Outline Overlay Guide -->
                        <div class="face-oval-guide" id="faceGuide" style="display: none;"></div>
                    </div>

                    <!-- Hidden Canvas for frame capture -->
                    <canvas id="captureCanvas" style="display: none;"></canvas>

                    <!-- Desktop Snapshot Trigger (Shown only when live webcam is streaming) -->
                    <div class="mb-2" id="desktopCaptureBar" style="display: none;">
                        <button type="button" class="btn btn-warning fw-bold text-dark w-100 py-2" id="btnCapturePhoto">
                            <i class="fa-solid fa-camera-retro me-1"></i> Snap Photo Now
                        </button>
                    </div>

                    <!-- Camera Controls Buttons: Live Selfie Only -->
                    <div class="d-flex flex-column gap-2 mb-3">
                        <button type="button" class="btn btn-ub-primary py-2 shadow-sm" id="btnTriggerCamera"
                            style="border-radius: 8px; font-weight: 700;">
                            <i class="fa-solid fa-camera me-2"></i> Take Selfie Photo
                        </button>

                        <button type="button" class="btn btn-outline-danger py-2" id="btnRetakePhoto"
                            style="{{ old('profile_photo_data') ? 'display: block;' : 'display: none;' }}; border-radius: 8px; font-weight: 600; font-size: 13px;">
                            <i class="fa-solid fa-rotate-right me-1"></i> Retake Selfie
                        </button>
                    </div>

                    <!-- Verification Feedback Status -->
                    <div id="photoStatusAlert" class="mb-3 text-center">
                        @if (old('profile_photo_data'))
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                <i class="fa-solid fa-circle-check me-1"></i> Photo ready! Click Proceed below.
                            </span>
                        @else
                            <span class="badge bg-light text-muted border px-3 py-2" style="font-size: 11px;">
                                <i class="fa-solid fa-circle-info text-primary me-1"></i> A selfie is required to unlock
                                Step 2
                            </span>
                        @endif
                    </div>

                    <!-- Proceed to Step 2 Button -->
                    <button type="button" class="btn btn-auth-primary-clean w-100 mb-3" id="btnProceedToStep2"
                        {{ old('profile_photo_data') ? '' : 'disabled' }}>
                        Proceed to Step 2: Details <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center pt-2 border-top">
                        <p class="small text-muted mb-0">
                            Already registered?
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none"
                                style="color: var(--ub-maroon);">
                                Log in here
                            </a>
                        </p>
                    </div>
                </div>

                <!-- ================= STEP 2: STUDENT DETAILS ================= -->
                <div id="stepSection2" style="display: none;">
                    <!-- Mini Selfie Badge Preview on Step 2 -->
                    <div class="d-flex align-items-center justify-content-between p-2 mb-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <img id="step2ThumbImg" src="{{ old('profile_photo_data') ?? '' }}" alt="Verified Selfie"
                                style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--ub-maroon);">
                            <div>
                                <div class="fw-bold small text-dark"><i
                                        class="fa-solid fa-circle-check text-success me-1"></i> Selfie Verified</div>
                                <div class="text-muted" id="selfieBadgeSubtitle" style="font-size: 11px;">Saved for borrower profile &amp; QR</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2"
                            style="font-size: 11px;" onclick="goToStep(1)">
                            <i class="fa-solid fa-camera-rotate me-1"></i> Change
                        </button>
                    </div>

                    <!-- Role Dropdown: Student or Teacher / Faculty -->
                    <div class="mb-2">
                        <label for="role" class="form-label small fw-semibold text-secondary mb-1">
                            <i class="fa-solid fa-user-tag me-1 text-warning"></i> Register As <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="role" class="form-select auth-input-clean @error('role') is-invalid @enderror" required>
                            <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>
                                🎓 Student
                            </option>
                            <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>
                                👨‍🏫 Teacher / Faculty Member
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div class="mb-2">
                        <input type="text" name="name" id="name"
                            class="form-control auth-input-clean @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Full Name (e.g. Juan C. Dela Cruz)" required
                            autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Dual Column: Student ID & College -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" name="student_id" id="student_id"
                                class="form-control auth-input-clean @error('student_id') is-invalid @enderror"
                                value="{{ old('student_id') }}" placeholder="Student ID No." required>
                            @error('student_id')
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="department" id="department"
                                class="form-control auth-input-clean @error('department') is-invalid @enderror"
                                value="{{ old('department') }}" placeholder="College / Program" required>
                            @error('department')
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Official Student Email -->
                    <div class="mb-2">
                        <div class="input-group">
                            <input type="text" name="email" id="email"
                                class="form-control auth-input-clean @error('email') is-invalid @enderror"
                                value="{{ old('email') ? preg_replace('/@ub\.edu\.ph$/i', '', old('email')) : '' }}"
                                placeholder="Student UB Gmail Username" required
                                autocomplete="username">
                            <span class="input-group-text bg-light text-muted small fw-semibold border-start-0"
                                style="border-radius: 0 8px 8px 0; border: 1.5px solid #cbd5e1; border-left: none;">
                                @ub.edu.ph
                            </span>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Mobile Phone (Optional) -->
                    <div class="mb-2">
                        <input type="tel" name="phone_number" id="phone_number"
                            class="form-control auth-input-clean @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number') }}" placeholder="Mobile Phone (Optional)" autocomplete="tel">
                        @error('phone_number')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Passwords -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="password" name="password" id="reg_password"
                                    class="form-control auth-input-clean @error('password') is-invalid @enderror"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
                                    placeholder="Password" required autocomplete="new-password">
                                <button type="button" class="password-toggle-btn-clean"
                                    onclick="toggleFieldVisibility('reg_password', this)" tabindex="-1">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="reg_password_conf"
                                    class="form-control auth-input-clean"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
                                    placeholder="Confirm" required autocomplete="new-password">
                                <button type="button" class="password-toggle-btn-clean"
                                    onclick="toggleFieldVisibility('reg_password_conf', this)" tabindex="-1">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions: Back to Step 1 & Complete Registration -->
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-outline-secondary" id="btnBackToStep1"
                            style="border-radius: 8px; font-weight: 600;" onclick="goToStep(1)">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </button>
                        <button type="submit" class="btn btn-auth-primary-clean flex-grow-1" id="btnSubmitRegistration">
                            Complete & Send OTP <i class="fa-solid fa-circle-check ms-1"></i>
                        </button>
                    </div>

                    <div class="text-center pt-2 border-top">
                        <p class="small text-muted mb-0">
                            Already registered?
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none"
                                style="color: var(--ub-maroon);">
                                Log in here
                            </a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Full-Screen Submitting Loading Overlay (Prevents interactions & provides rich feedback) -->
    <div id="registrationLoadingOverlay" class="auth-loading-overlay" aria-hidden="true" role="dialog"
        aria-modal="true" aria-label="Registration in progress">
        <div class="auth-loading-card">
            <!-- Glowing Orbiting Spinner & UB Graduation Cap Emblem -->
            <div class="auth-loading-spinner-wrapper">
                <div class="auth-loading-ring-outer"></div>
                <div class="auth-loading-ring-inner"></div>
                <div class="auth-loading-icon-center">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>

            <!-- Title & Dynamic Subtitle -->
            <h4 class="auth-loading-title">Submitting Registration</h4>
            <div class="auth-loading-subtitle" id="authLoadingSubtext">
                Creating your student profile & preparing credentials...
            </div>

            <!-- Animated Shimmer Progress Bar -->
            <div class="auth-loading-progress-track">
                <div class="auth-loading-progress-bar"></div>
            </div>

            <!-- Multi-step Staged Progress Checklist -->
            <div class="auth-loading-steps">
                <div class="auth-loading-step-item completed" id="stageStep1">
                    <i class="fa-solid fa-circle-check text-success"></i>
                    <span>Student Information & Selfie Verified</span>
                </div>
                <div class="auth-loading-step-item active" id="stageStep2">
                    <i class="fa-solid fa-circle-notch fa-spin text-danger"></i>
                    <span>Creating Account & Securing QR Token</span>
                </div>
                <div class="auth-loading-step-item" id="stageStep3">
                    <i class="fa-regular fa-envelope"></i>
                    <span>Sending 6-Digit OTP to UB Gmail</span>
                </div>
            </div>

            <!-- Safety Note & Lock -->
            <div class="auth-loading-footer">
                <i class="fa-solid fa-shield-halved text-muted"></i>
                <span>Please do not refresh, close, or press back.</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cameraStream = null;
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('captureCanvas');
        const photoPreview = document.getElementById('photoPreviewImg');
        const photoDataInput = document.getElementById('profilePhotoData');
        const cameraPlaceholder = document.getElementById('cameraPlaceholder');
        const cameraFrame = document.getElementById('cameraFrame');
        const faceGuide = document.getElementById('faceGuide');
        const btnTriggerCamera = document.getElementById('btnTriggerCamera');
        const btnTriggerUpload = document.getElementById('btnTriggerUpload');
        const btnCapture = document.getElementById('btnCapturePhoto');
        const btnRetake = document.getElementById('btnRetakePhoto');
        const btnProceed = document.getElementById('btnProceedToStep2');
        const photoStatus = document.getElementById('photoStatusAlert');
        const desktopCaptureBar = document.getElementById('desktopCaptureBar');
        const nativeCameraInput = document.getElementById('nativeCameraInput');
        const step2Thumb = document.getElementById('step2ThumbImg');

        // Check if device is mobile or if getUserMedia is securely available
        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const hasSecureWebcam = !isMobile && window.isSecureContext && navigator.mediaDevices && navigator.mediaDevices
            .getUserMedia;

        // Handle "Take Selfie Photo" button & clicking on camera frame
        function triggerCameraAction() {
            if (hasSecureWebcam) {
                startDesktopWebcam();
            } else {
                // Natively opens front selfie shutter on iOS (Safari) and Android without HTTPS blocking!
                nativeCameraInput.click();
            }
        }

        btnTriggerCamera.addEventListener('click', triggerCameraAction);
        cameraFrame.addEventListener('click', function() {
            if (!cameraStream && !photoDataInput.value) {
                triggerCameraAction();
            }
        });

        // Handle selfie capture from native front camera input
        nativeCameraInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                processAndSetPhoto(e.target.result);
            };
            reader.readAsDataURL(file);
        });

        // Desktop Live Webcam Handler
        async function startDesktopWebcam() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 640
                        },
                        height: {
                            ideal: 640
                        }
                    },
                    audio: false
                });
                video.srcObject = cameraStream;
                video.style.display = 'block';
                faceGuide.style.display = 'block';
                cameraPlaceholder.style.display = 'none';
                photoPreview.style.display = 'none';
                desktopCaptureBar.style.display = 'block';
                btnTriggerCamera.style.display = 'none';
                btnRetake.style.display = 'none';
                photoStatus.innerHTML =
                    '<span class="badge bg-warning text-dark border px-3 py-2"><i class="fa-solid fa-face-smile me-1"></i> Look at the camera and click "Snap Photo Now"</span>';
            } catch (err) {
                console.warn('Webcam fallback to front camera input:', err);
                nativeCameraInput.click();
            }
        }

        // Capture from Desktop Webcam
        btnCapture.addEventListener('click', function() {
            if (!video.videoWidth) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');

            // Mirror horizontally
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.88);
            processAndSetPhoto(dataUrl);
            stopCamera();
        });

        // Process and display the photo
        function processAndSetPhoto(dataUrl) {
            photoDataInput.value = dataUrl;
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';
            step2Thumb.src = dataUrl;

            video.style.display = 'none';
            faceGuide.style.display = 'none';
            cameraPlaceholder.style.display = 'none';
            desktopCaptureBar.style.display = 'none';
            btnTriggerCamera.style.display = 'none';
            btnRetake.style.display = 'block';
            btnProceed.disabled = false;

            photoStatus.innerHTML =
                '<span class="badge bg-success-subtle text-success border border-success px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Selfie captured! Click Proceed below.</span>';

            if (typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: 'Selfie verified! Proceed to Step 2.'
                });
            }
        }

        // Retake Photo
        btnRetake.addEventListener('click', function() {
            photoDataInput.value = '';
            nativeCameraInput.value = '';
            photoPreview.style.display = 'none';
            cameraPlaceholder.style.display = 'flex';
            btnTriggerCamera.style.display = 'block';
            btnRetake.style.display = 'none';
            btnProceed.disabled = true;
            photoStatus.innerHTML =
                '<span class="badge bg-light text-muted border px-3 py-2" style="font-size: 11px;"><i class="fa-solid fa-circle-info text-primary me-1"></i> A selfie is required to unlock Step 2</span>';

            triggerCameraAction();
        });

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        // Stepper Navigation
        function goToStep(step) {
            if (step === 2) {
                if (!photoDataInput.value) {
                    alert('Please take your selfie photo in Step 1 first.');
                    return;
                }
                stopCamera();
                document.getElementById('stepSection1').style.display = 'none';
                document.getElementById('stepSection2').style.display = 'block';

                document.getElementById('stepperNav1').classList.remove('active');
                document.getElementById('stepperNav1').classList.add('completed');
                document.getElementById('stepperCircle1').innerHTML =
                    '<i class="fa-solid fa-check" style="font-size: 11px;"></i>';
                document.getElementById('stepperLine').classList.add('completed');
                document.getElementById('stepperNav2').classList.add('active');

                document.getElementById('name').focus();
            } else {
                document.getElementById('stepSection2').style.display = 'none';
                document.getElementById('stepSection1').style.display = 'block';

                document.getElementById('stepperNav2').classList.remove('active');
                document.getElementById('stepperNav1').classList.remove('completed');
                document.getElementById('stepperNav1').classList.add('active');
                document.getElementById('stepperCircle1').textContent = '1';
                document.getElementById('stepperLine').classList.remove('completed');
            }
        }

        btnProceed.addEventListener('click', function() {
            goToStep(2);
        });

        // Auto navigate to step 2 if returning with validation errors from step 2
        @if ($errors->any() && !$errors->has('profile_photo'))
            if (photoDataInput.value) {
                goToStep(2);
            }
        @endif

        // Auto clean email field if user pastes or types @ub.edu.ph
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                if (this.value.includes('@ub.edu.ph')) {
                    this.value = this.value.replace(/@ub\.edu\.ph/gi, '').trim();
                }
            });
            emailInput.addEventListener('blur', function() {
                this.value = this.value.replace(/@ub\.edu\.ph/gi, '').trim();
            });
        }

        function toggleFieldVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // ================= SUBMISSION LOADING OVERLAY & MULTI-CLICK GUARD =================
        const registerForm = document.getElementById('multiStepRegisterForm');
        const submitBtn = document.getElementById('btnSubmitRegistration');
        const backBtn = document.getElementById('btnBackToStep1');
        const loadingOverlay = document.getElementById('registrationLoadingOverlay');
        const loadingSubtext = document.getElementById('authLoadingSubtext');
        const stageStep1 = document.getElementById('stageStep1');
        const stageStep2 = document.getElementById('stageStep2');
        const stageStep3 = document.getElementById('stageStep3');
        let isFormSubmitting = false;
        let stageInterval = null;

        function showLoadingOverlay() {
            if (!loadingOverlay) return;
            loadingOverlay.classList.add('active');
            loadingOverlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...';
            }
            if (backBtn) {
                backBtn.disabled = true;
            }

            // Start dynamic staged progress updates to keep the student informed during mail server latency
            let elapsed = 0;
            stageInterval = setInterval(() => {
                elapsed += 1;
                if (elapsed === 2) {
                    if (stageStep2) {
                        stageStep2.className = 'auth-loading-step-item completed';
                        stageStep2.innerHTML = '<i class="fa-solid fa-circle-check text-success"></i> <span>Account & Student Pass Created</span>';
                    }
                    if (stageStep3) {
                        stageStep3.className = 'auth-loading-step-item active';
                        stageStep3.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-danger"></i> <span>Connecting to UB Mail Gateway...</span>';
                    }
                    if (loadingSubtext) {
                        loadingSubtext.textContent = 'Connecting to official UB Mail server...';
                    }
                } else if (elapsed === 5) {
                    if (stageStep3) {
                        stageStep3.innerHTML = '<i class="fa-solid fa-paper-plane text-danger"></i> <span>Dispatching 6-Digit OTP to your Gmail...</span>';
                    }
                    if (loadingSubtext) {
                        loadingSubtext.textContent = 'Sending verification code to your UB Gmail inbox...';
                    }
                } else if (elapsed === 9) {
                    if (loadingSubtext) {
                        loadingSubtext.textContent = 'Finalizing setup, redirecting to OTP verification...';
                    }
                }
            }, 1000);
        }

        function hideLoadingOverlay() {
            isFormSubmitting = false;
            if (stageInterval) {
                clearInterval(stageInterval);
                stageInterval = null;
            }
            if (loadingOverlay) {
                loadingOverlay.classList.remove('active');
                loadingOverlay.setAttribute('aria-hidden', 'true');
            }
            document.body.style.overflow = '';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Complete & Send OTP <i class="fa-solid fa-circle-check ms-1"></i>';
            }
            if (backBtn) {
                backBtn.disabled = false;
            }
        }

        // Dynamic role placeholder switching
        function updateRoleFormDetails() {
            const roleSelect = document.getElementById('role');
            if (!roleSelect) return;
            const role = roleSelect.value;
            const studentIdInput = document.getElementById('student_id');
            const deptInput = document.getElementById('department');
            const emailInput = document.getElementById('email');
            const subtitleEl = document.getElementById('selfieBadgeSubtitle');

            if (role === 'teacher') {
                if (studentIdInput) studentIdInput.placeholder = 'Faculty / Employee ID No.';
                if (deptInput) deptInput.placeholder = 'Department / College (e.g. CCS, CBA)';
                if (emailInput) emailInput.placeholder = 'Teacher UB Gmail Username';
                if (subtitleEl) subtitleEl.textContent = 'Saved for faculty profile & QR';
            } else {
                if (studentIdInput) studentIdInput.placeholder = 'Student ID No. (e.g. UB-2024-00123)';
                if (deptInput) deptInput.placeholder = 'College / Program (e.g. BSIT, BSHM)';
                if (emailInput) emailInput.placeholder = 'Student UB Gmail Username';
                if (subtitleEl) subtitleEl.textContent = 'Saved for student profile & QR';
            }
        }

        const roleElem = document.getElementById('role');
        if (roleElem) {
            roleElem.addEventListener('change', updateRoleFormDetails);
            updateRoleFormDetails();
        }

        // Restore form if page is restored from browser cache (bfcache)
        window.addEventListener('pageshow', function() {
            hideLoadingOverlay();
            updateRoleFormDetails();
        });

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                // Prevent duplicate submission if already submitting
                if (isFormSubmitting) {
                    e.preventDefault();
                    return false;
                }

                // 1. Verify that selfie photo was captured in Step 1
                if (!photoDataInput || !photoDataInput.value) {
                    e.preventDefault();
                    goToStep(1);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Selfie Photo Required',
                            text: 'Please take your front selfie photo in Step 1 before submitting.',
                            confirmButtonColor: '#7B1113'
                        });
                    } else {
                        alert('Please take your front selfie photo in Step 1 before submitting.');
                    }
                    return false;
                }

                // 2. Check native HTML5 form validity
                if (!registerForm.checkValidity()) {
                    registerForm.reportValidity();
                    return false;
                }

                // 3. Check password confirmation match
                const pwd = document.getElementById('reg_password');
                const pwdConf = document.getElementById('reg_password_conf');
                if (pwd && pwdConf && pwd.value !== pwdConf.value) {
                    e.preventDefault();
                    pwdConf.focus();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Mismatch',
                            text: 'Your password confirmation does not match.',
                            confirmButtonColor: '#7B1113'
                        });
                    } else {
                        alert('Your password confirmation does not match.');
                    }
                    return false;
                }

                // 4. Check password minimum length
                if (pwd && pwd.value.length < 8) {
                    e.preventDefault();
                    pwd.focus();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Too Short',
                            text: 'Password must be at least 8 characters.',
                            confirmButtonColor: '#7B1113'
                        });
                    } else {
                        alert('Password must be at least 8 characters.');
                    }
                    return false;
                }

                // 5. Validations passed -> Show full loading UI & lock interaction
                isFormSubmitting = true;
                showLoadingOverlay();
            });
        }
    </script>
@endpush
