<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BorrowingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'student_id',
        'staff_id',
        'returned_by_staff_id',
        'borrow_date_time',
        'due_date_time',
        'return_date_time',
        'proof_of_borrowing',
        'proof_of_return',
        'status',
        'penalty_per_day',
        'penalty_days',
        'total_penalty',
        'penalty_status',
        'penalty_paid_at',
        'staff_notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date_time' => 'datetime',
            'due_date_time' => 'datetime',
            'return_date_time' => 'datetime',
            'penalty_paid_at' => 'datetime',
            'penalty_per_day' => 'decimal:2',
            'total_penalty' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function returnedByStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BorrowedItem::class, 'transaction_id');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(EmailNotificationLog::class, 'transaction_id');
    }

    /**
     * Calculate and update running penalty
     */
    public function calculatePenalty(?Carbon $targetDate = null): array
    {
        $target = $targetDate ?? ($this->return_date_time ?? now());
        $due = $this->due_date_time;

        if (!$due) {
            return ['days' => 0, 'amount' => 0.00];
        }

        // Compare calendar dates (e.g. Due Sept 9 -> penalty starts Sept 10 = 1 day = 5 pesos)
        $dueDay = $due->copy()->startOfDay();
        $targetDay = $target->copy()->startOfDay();

        $daysLate = 0;
        if ($targetDay->greaterThan($dueDay)) {
            $daysLate = (int) $dueDay->diffInDays($targetDay);
        }

        $penaltyPerDay = $this->penalty_per_day ?? 5.00;
        $amount = $daysLate * $penaltyPerDay;

        // If returned and penalty was previously paid, do not overwrite paid status
        if ($this->status !== 'returned') {
            $isOverdue = now()->greaterThan($due);
            $this->status = $isOverdue ? 'overdue' : 'ongoing';
            $this->penalty_days = $daysLate;
            $this->total_penalty = $amount;
            if ($amount > 0 && $this->penalty_status === 'none') {
                $this->penalty_status = 'unpaid';
            }
            $this->saveQuietly();
        }

        return [
            'days' => $daysLate,
            'amount' => $amount,
        ];
    }

    /**
     * Helper to check if item is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'returned') {
            return false;
        }
        return now()->greaterThan($this->due_date_time);
    }

    /**
     * Get count of returned items
     */
    public function returnedItemsCount(): int
    {
        return $this->items->where('status', 'returned')->count();
    }

    /**
     * Get total count of borrowed items
     */
    public function totalItemsCount(): int
    {
        return $this->items->count();
    }

    /**
     * Get count of pending (unreturned) items
     */
    public function pendingItemsCount(): int
    {
        return $this->items->where('status', '!=', 'returned')->count();
    }

    /**
     * Check if all items in the transaction are returned
     */
    public function isFullyReturned(): bool
    {
        $total = $this->totalItemsCount();
        return $total > 0 && $this->returnedItemsCount() === $total;
    }

    /**
     * Return progress formatted e.g. "1/3"
     */
    public function returnProgress(): string
    {
        return $this->returnedItemsCount() . '/' . $this->totalItemsCount();
    }

    /**
     * Return percentage formatted e.g. 33
     */
    public function returnProgressPercent(): int
    {
        $total = $this->totalItemsCount();
        if ($total === 0) return 100;
        return (int) round(($this->returnedItemsCount() / $total) * 100);
    }
}
