<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'request_date',
        'status',
        'approved_by',
        'approval_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'date',
        'approval_date' => 'date',
    ];

    /**
     * Get the user that owns the cash advance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that approved the cash advance.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
