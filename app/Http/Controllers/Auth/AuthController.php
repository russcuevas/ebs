<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\StudentOtpMail;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginInput = trim((string) $request->input('email', ''));
        if (!empty($loginInput) && !str_contains($loginInput, '@')) {
            $userMatch = User::where('student_id', $loginInput)->first();
            if ($userMatch) {
                $loginInput = $userMatch->email;
            } else {
                $loginInput .= '@ub.edu.ph';
            }
            $request->merge(['email' => strtolower($loginInput)]);
        }

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'blocked') {
                Auth::logout();
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Your account is blocked. Please contact the administrator.',
                ]);
            }

            // Check if student email is verified
            if ($user->isStudent() && !$user->email_verified_at) {
                return redirect()->route('verification.notice');
            }

            return $this->redirectBasedOnRole($user);
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Invalid email or password credentials.',
        ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rawEmail = trim((string) $request->input('email', ''));
        if (!empty($rawEmail)) {
            // Remove any trailing @ub.edu.ph if entered/pasted, then append @ub.edu.ph if no domain is provided
            $rawEmail = preg_replace('/(@ub\.edu\.ph)+$/i', '', $rawEmail);
            if (!str_contains($rawEmail, '@')) {
                $rawEmail .= '@ub.edu.ph';
            }
            $request->merge(['email' => strtolower($rawEmail)]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', 'unique:users,student_id'],
            'department' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@ub.edu.ph')) {
                        $fail('The email address must end with @ub.edu.ph (Official UB Student Email).');
                    }
                },
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Please provide your full name.',
            'student_id.required' => 'Please enter your Student ID number.',
            'student_id.unique' => 'This Student ID is already registered.',
            'department.required' => 'Please specify your department / program.',
            'email.required' => 'Please provide your official UB email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'A password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Process and save student selfie / profile photo
        $photoPath = null;

        if ($request->filled('profile_photo_data') && str_starts_with($request->profile_photo_data, 'data:image')) {
            $data = $request->profile_photo_data;
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $decoded = base64_decode($data);
                    if ($decoded !== false) {
                        $filename = 'student_' . preg_replace('/[^A-Za-z0-9]/', '', $request->student_id) . '_' . time() . '.' . ($type === 'jpeg' ? 'jpg' : $type);
                        $dir = public_path('uploads/avatars');
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        file_put_contents($dir . '/' . $filename, $decoded);
                        $photoPath = 'uploads/avatars/' . $filename;
                    }
                }
            }
        } elseif ($request->hasFile('profile_photo_file')) {
            $file = $request->file('profile_photo_file');
            $filename = 'student_' . preg_replace('/[^A-Za-z0-9]/', '', $request->student_id) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $dir = public_path('uploads/avatars');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $photoPath = 'uploads/avatars/' . $filename;
        }

        if (!$photoPath) {
            return back()->withInput()->withErrors(['profile_photo' => 'Please take a clear selfie photo in Step 1 before submitting your registration.']);
        }

        $qrToken = 'UB-STUD-' . strtoupper(Str::random(6)) . '-' . preg_replace('/[^A-Za-z0-9]/', '', $request->student_id);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => 'student',
            'department' => $request->department,
            'student_id' => $request->student_id,
            'qr_code_token' => $qrToken,
            'phone_number' => $request->phone_number,
            'profile_photo_path' => $photoPath,
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        // Generate and send OTP
        $this->generateAndSendOtp($user);

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Registration submitted! Please check your UB Gmail for your verification code.');
    }

    public function showOtpVerification()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->email_verified_at) {
            return $this->redirectBasedOnRole($user);
        }

        return view('auth.verify-otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size' => 'The code must be exactly 6 digits.',
        ]);

        $user = Auth::user();

        $otpRecord = OtpVerification::where('email', $user->email)
            ->where('is_used', false)
            ->orderBy('id', 'desc')
            ->first();

        if (!$otpRecord || !$otpRecord->isValid($request->otp)) {
            return back()->withInput()->withErrors([
                'otp' => 'Invalid or expired verification code. Please request a new code.',
            ]);
        }

        $otpRecord->update(['is_used' => true]);
        $user->update(['email_verified_at' => now()]);

        return redirect()->route('student.dashboard')->with('success', 'Account verified successfully! Here is your official Student QR Code.');
    }

    public function resendOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->generateAndSendOtp($user);

        return back()->with('success', 'A new 6-digit verification code has been sent to ' . $user->email . '.');
    }

    protected function generateAndSendOtp(User $user): void
    {
        // Invalidate past unused codes
        OtpVerification::where('email', $user->email)->update(['is_used' => true]);

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'email' => $user->email,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(15),
            'is_used' => false,
        ]);

        try {
            Mail::to($user->email)->send(new StudentOtpMail($user->name, $otp, 15));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$user->email}: " . $e->getMessage());
            // Flash OTP in session for local testing fallback if SMTP encounters network issues
            session()->flash('dev_otp_fallback', "Dev Notice (Fallback): Your verification code is {$otp}");
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}
