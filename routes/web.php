<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\DailySaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\Employee\DailySaleController as EmployeeDailySaleController;
use App\Http\Controllers\Employee\SalaryController as EmployeeSalaryController;
use App\Http\Controllers\Employee\CashAdvanceController as EmployeeCashAdvanceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration routes (accessible only by admin in a real app, but keeping it public for demo)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Daily Sales Management
    Route::resource('daily-sales', DailySaleController::class);
    Route::post('/daily-sales/{id}/verify', [DailySaleController::class, 'verify'])->name('daily-sales.verify');
    Route::post('/daily-sales/{id}/unverify', [DailySaleController::class, 'unverify'])->name('daily-sales.unverify');

    // Salary Management
    Route::resource('salaries', SalaryController::class);

    // Cash Advance Management
    Route::resource('cash-advances', CashAdvanceController::class);
    Route::post('/cash-advances/{id}/approve', [CashAdvanceController::class, 'approve'])->name('cash-advances.approve');
    Route::post('/cash-advances/{id}/reject', [CashAdvanceController::class, 'reject'])->name('cash-advances.reject');

    // Inventory Management
    Route::resource('inventory', InventoryController::class);
    Route::put('/inventory/{id}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');

    // User Management
    Route::resource('users', UserController::class);
});

// Employee Routes
Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'employeeDashboard'])->name('dashboard');

    // Daily Sales (use employee controller)
    Route::get('/daily-sales', [EmployeeDailySaleController::class, 'index'])->name('daily-sales.index');
    Route::get('/daily-sales/create', [EmployeeDailySaleController::class, 'create'])->name('daily-sales.create');
    Route::post('/daily-sales', [EmployeeDailySaleController::class, 'store'])->name('daily-sales.store');
    Route::get('/daily-sales/{id}', [EmployeeDailySaleController::class, 'show'])->name('daily-sales.show');
    Route::get('/daily-sales/{id}/edit', [EmployeeDailySaleController::class, 'edit'])->name('daily-sales.edit');
    Route::put('/daily-sales/{id}', [EmployeeDailySaleController::class, 'update'])->name('daily-sales.update');

    // Cash Advance requests (use employee controller)
    Route::get('/cash-advances', [EmployeeCashAdvanceController::class, 'index'])->name('cash-advances.index');
    Route::get('/cash-advances/create', [EmployeeCashAdvanceController::class, 'create'])->name('cash-advances.create');
    Route::post('/cash-advances', [EmployeeCashAdvanceController::class, 'store'])->name('cash-advances.store');
    Route::get('/cash-advances/{id}', [EmployeeCashAdvanceController::class, 'show'])->name('cash-advances.show');
    Route::get('/cash-advances/{id}/edit', [EmployeeCashAdvanceController::class, 'edit'])->name('cash-advances.edit');
    Route::put('/cash-advances/{id}', [EmployeeCashAdvanceController::class, 'update'])->name('cash-advances.update');
    Route::delete('/cash-advances/{id}', [EmployeeCashAdvanceController::class, 'destroy'])->name('cash-advances.destroy');

    // View salary (fixed, no parameter needed)
    Route::get('/salary', [EmployeeSalaryController::class, 'show'])->name('salary.show');
});
