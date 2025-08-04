<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sale_date',
        'dough_brought',
        'dough_remaining_printed',
        'dough_remaining_unprinted',
        'total_sales',
        'admin_share',
        'employee_share',
        'employee_expenses',
        'unsold_pastries',
        'notes',
        'is_verified',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'dough_brought' => 'decimal:2',
        'dough_remaining_printed' => 'integer',
        'dough_remaining_unprinted' => 'integer',
        'total_sales' => 'decimal:2',
        'admin_share' => 'decimal:2',
        'employee_share' => 'decimal:2',
        'employee_expenses' => 'decimal:2',
        'unsold_pastries' => 'integer',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the daily sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
