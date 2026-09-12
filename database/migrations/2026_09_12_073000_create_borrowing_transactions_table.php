<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('returned_by_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('borrow_date_time');
            $table->dateTime('due_date_time');
            $table->dateTime('return_date_time')->nullable();
            $table->string('proof_of_borrowing')->nullable();
            $table->string('proof_of_return')->nullable();
            $table->string('status')->default('ongoing'); // ongoing, returned, overdue
            $table->decimal('penalty_per_day', 8, 2)->default(5.00);
            $table->integer('penalty_days')->default(0);
            $table->decimal('total_penalty', 8, 2)->default(0.00);
            $table->string('penalty_status')->default('none'); // none, unpaid, paid, waived
            $table->timestamp('penalty_paid_at')->nullable();
            $table->text('staff_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing_transactions');
    }
};
