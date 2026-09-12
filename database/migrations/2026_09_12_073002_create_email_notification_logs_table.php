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
        Schema::create('email_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('borrowing_transactions')->cascadeOnDelete();
            $table->string('type'); // due_reminder_1_day, overdue_notice
            $table->string('recipient_email');
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_logs');
    }
};
