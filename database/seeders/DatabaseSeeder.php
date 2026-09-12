<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BorrowingTransaction;
use App\Models\BorrowedItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@ub.edu.ph'],
            [
                'name' => 'UB System Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department' => 'Management Information Systems',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Staff Account
        $staff = User::firstOrCreate(
            ['email' => 'staff@ub.edu.ph'],
            [
                'name' => 'Bantay Staff - CCS',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'department' => 'College of Computer Studies',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Verified Student Account
        $student = User::firstOrCreate(
            ['email' => 'student@ub.edu.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'department' => 'College of Computer Studies',
                'student_id' => 'UB-2024-00123',
                'qr_code_token' => 'UB-STUD-202400123-JUAN',
                'phone_number' => '09171234567',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Sample Completed/Returned Borrowing for demonstration
        $transaction = BorrowingTransaction::create([
            'reference_no' => 'EBS-' . date('Ymd') . '-0001',
            'student_id' => $student->id,
            'staff_id' => $staff->id,
            'returned_by_staff_id' => $staff->id,
            'borrow_date_time' => now()->subDays(3)->setTime(9, 0),
            'due_date_time' => now()->subDays(2)->setTime(17, 0),
            'return_date_time' => now()->subDays(2)->setTime(16, 30),
            'status' => 'returned',
            'penalty_per_day' => 5.00,
            'penalty_days' => 0,
            'total_penalty' => 0.00,
            'penalty_status' => 'none',
            'staff_notes' => 'Returned on time in good condition.',
        ]);

        BorrowedItem::create([
            'transaction_id' => $transaction->id,
            'item_name' => 'HDMI Cable 5m',
            'item_location' => 'CCS AV Room / Lab 3',
            'status' => 'returned',
        ]);

        BorrowedItem::create([
            'transaction_id' => $transaction->id,
            'item_name' => 'Digital Projector Remote',
            'item_location' => 'CCS AV Room',
            'status' => 'returned',
        ]);
    }
}
