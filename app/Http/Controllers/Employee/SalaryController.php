<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\DailySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the employee's salary information.
     */
    public function show()
    {
        $user = Auth::user();
        $salary = $user->salary;

        if (!$salary) {
            abort(404, 'Data gaji belum tersedia. Silakan hubungi admin.');
        }

        // Get verified sales for current month
        $currentMonth = Carbon::now()->startOfMonth();
        $monthlySales = DailySale::where('user_id', $user->id)
            ->where('is_verified', true)
            ->where('sale_date', '>=', $currentMonth)
            ->sum('total_sales');

        $monthlyEarnings = DailySale::where('user_id', $user->id)
            ->where('is_verified', true)
            ->where('sale_date', '>=', $currentMonth)
            ->sum('employee_share');

        // Get sales history
        $salesHistory = DailySale::where('user_id', $user->id)
            ->orderBy('sale_date', 'desc')
            ->paginate(10, ['*'], 'sales_page');

        // Get cash advances history
        $advancesHistory = CashAdvance::where('user_id', $user->id)
            ->orderBy('request_date', 'desc')
            ->paginate(10, ['*'], 'advances_page');

        return view('employee.salary.index', compact('salary', 'salesHistory', 'advancesHistory', 'monthlySales', 'monthlyEarnings'));
    }
}
