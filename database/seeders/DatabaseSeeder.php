<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Salary;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Employee account
        $employee = User::create([
            'name' => 'Karyawan',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        // Create salary record for employee
        Salary::create([
            'user_id' => $employee->id,
            'total_earned' => 0,
            'total_advances' => 0,
            'net_salary' => 0,
            'last_updated' => now(),
        ]);
    }
}
