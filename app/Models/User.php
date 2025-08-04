<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
    ];

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

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    /**
     * Get all daily sales for the user.
     */
    public function dailySales()
    {
        return $this->hasMany(DailySale::class);
    }

    /**
     * Get the salary record associated with the user.
     */
    public function salary()
    {
        return $this->hasOne(Salary::class);
    }

    /**
     * Get all cash advances for the user.
     */
    public function cashAdvances()
    {
        return $this->hasMany(CashAdvance::class);
    }

    /**
     * Get all inventory items added by the user.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'added_by');
    }

    /**
     * Get all cash advances approved by the user.
     */
    public function approvedCashAdvances()
    {
        return $this->hasMany(CashAdvance::class, 'approved_by');
    }
}
