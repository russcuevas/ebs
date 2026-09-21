<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [AuthController::class, 'showOtpVerification'])->name('verification.notice');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verification.verify');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('verification.resend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Staff Management
    Route::resource('staff', Admin\StaffController::class);

    // Student Management
    Route::get('/students/{student}/print', [Admin\StudentController::class, 'printCard'])->name('students.print');
    Route::resource('students', Admin\StudentController::class);
    Route::post('/students/{student}/toggle-status', [Admin\StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::post('/students/{student}/verify-email', [Admin\StudentController::class, 'verifyEmail'])->name('students.verify-email');

    // Borrowing Transactions Monitoring
    Route::get('/borrowings', [Admin\BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/{transaction}', [Admin\BorrowingController::class, 'show'])->name('borrowings.show');
    Route::post('/borrowings/{transaction}/send-reminder', [Admin\BorrowingController::class, 'sendReminderEmail'])->name('borrowings.send-reminder');

    // Penalties Monitoring
    Route::get('/penalties', [Admin\PenaltyController::class, 'index'])->name('penalties.index');
    Route::post('/penalties/{transaction}/settle', [Admin\PenaltyController::class, 'settle'])->name('penalties.settle');
    Route::post('/penalties/{transaction}/waive', [Admin\PenaltyController::class, 'waive'])->name('penalties.waive');
});

// Staff (Bantay) Routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/dashboard', [Staff\DashboardController::class, 'index'])->name('dashboard');

    // Student QR Scanner
    Route::get('/scanner', [Staff\ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/lookup', [Staff\ScannerController::class, 'lookupStudent'])->name('scanner.lookup');

    // Equipment Borrowing
    Route::get('/borrowings/create', [Staff\BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [Staff\BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/{transaction}', [Staff\BorrowingController::class, 'show'])->name('borrowings.show');

    // Return Management & Proof
    Route::get('/borrowings/{transaction}/return', [Staff\ReturnController::class, 'showReturnForm'])->name('borrowings.return');
    Route::post('/borrowings/{transaction}/return', [Staff\ReturnController::class, 'processReturn'])->name('borrowings.process-return');
    Route::post('/borrowings/{transaction}/settle-penalty', [Staff\ReturnController::class, 'settlePenalty'])->name('borrowings.settle-penalty');
    Route::post('/students/{student}/settle-penalties', [Staff\ReturnController::class, 'settleStudentPenalties'])->name('students.settle-penalties');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [Student\HistoryController::class, 'index'])->name('history');
    Route::get('/borrowings/{transaction}', [Student\HistoryController::class, 'show'])->name('borrowings.show');
});
