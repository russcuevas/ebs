<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_name',
        'item_location',
        'status',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(BorrowingTransaction::class, 'transaction_id');
    }
}
