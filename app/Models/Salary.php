<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_earned',
        'total_advances',
        'net_salary',
        'last_updated',
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'total_advances' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'last_updated' => 'date',
    ];

    /**
     * Get the user that owns the salary.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cash advances for the salary.
     */
    public function cashAdvances()
    {
        return $this->hasMany(CashAdvance::class, 'user_id', 'user_id');
    }
}
