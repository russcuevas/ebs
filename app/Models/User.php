<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'student_id',
        'qr_code_token',
        'phone_number',
        'profile_photo_path',
        'status',
        'email_verified_at',
    ];

    /**
     * Get avatar image URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_photo_path && file_exists(public_path($this->profile_photo_path))) {
            return asset($this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7B1113&color=F5B800&bold=true';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class, 'student_id');
    }

    public function staffBorrowings(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class, 'staff_id');
    }

    /**
     * Check if student has overdue borrowings or unpaid penalties.
     * Blocks borrowing if true.
     */
    public function hasOverdueOrUnpaidPenalties(): bool
    {
        // Check if any borrowing is ongoing but past due
        $now = now();
        $hasActiveOverdue = $this->borrowings()
            ->where('status', 'ongoing')
            ->where('due_date_time', '<', $now)
            ->exists();

        if ($hasActiveOverdue) {
            return true;
        }

        // Check if status is marked overdue
        $hasStatusOverdue = $this->borrowings()
            ->where('status', 'overdue')
            ->exists();

        if ($hasStatusOverdue) {
            return true;
        }

        // Check if there are unpaid penalties
        $hasUnpaidPenalties = $this->borrowings()
            ->where('penalty_status', 'unpaid')
            ->where('total_penalty', '>', 0)
            ->exists();

        return $hasUnpaidPenalties;
    }

    /**
     * Get total unpaid penalty amount
     */
    public function totalUnpaidPenalties(): float
    {
        // First, refresh running penalties on ongoing transactions
        $this->refreshOngoingPenalties();

        return (float) $this->borrowings()
            ->where('penalty_status', 'unpaid')
            ->sum('total_penalty');
    }

    /**
     * Helper to compute and update running penalties
     */
    public function refreshOngoingPenalties(): void
    {
        $ongoing = $this->borrowings()->where('status', '!=', 'returned')->get();
        foreach ($ongoing as $trans) {
            $trans->calculatePenalty();
        }
    }

    /**
     * Generate QR code Data URI / inline SVG
     */
    public function getQrCodeSvg(): string
    {
        $payload = $this->qr_code_token ?? ('UB-STUDENT-' . $this->id . '-' . md5($this->email));
        $options = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'outputBase64' => false,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::M,
            'addQuietzone' => true,
            'svgUseFillAttributes' => true,
            'svgAddXmlHeader' => false,
        ]);

        return (new QRCode($options))->render($payload);
    }
}
