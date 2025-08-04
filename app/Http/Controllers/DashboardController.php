<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\User;
use App\Models\Salary;
use App\Models\CashAdvance;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Admin dashboard showing all stats and summaries
     */
    public function adminDashboard(Request $request)
    {
        // Filter tanggal
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        // Get all employees
        $employees = User::where('role', 'employee')->get();

        // Get recent sales with date filter if provided
        $recentSalesQuery = DailySale::with('user')
            ->orderBy('sale_date', 'desc');

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $recentSalesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $recentSales = $recentSalesQuery->take(10)->get();

        // Get pending cash advances
        $pendingAdvances = CashAdvance::with('user')
            ->where('status', 'pending')
            ->orderBy('request_date', 'desc')
            ->get();

        // Get low stock inventory items
        $lowStockItems = Inventory::whereRaw('quantity <= reorder_point')
            ->get();

        // Calculate totals
        $todaySales = DailySale::whereDate('sale_date', today())->sum('total_sales');

        // Monthly sales with date filter
        $monthSalesQuery = DailySale::query();

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $monthSalesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            $monthSalesQuery->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year);
        }

        $monthSales = $monthSalesQuery->sum('total_sales');

        return view('admin.dashboard', compact(
            'employees',
            'recentSales',
            'pendingAdvances',
            'lowStockItems',
            'todaySales',
            'monthSales',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Employee dashboard showing personal stats and summaries
     */
    public function employeeDashboard(Request $request)
    {
        $user = Auth::user();

        // Filter tanggal
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        // Get user's daily sales with date filter if provided
        $recentSalesQuery = $user->dailySales()
            ->with('user')
            ->orderBy('sale_date', 'desc');

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $recentSalesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $recentSales = $recentSalesQuery->take(10)->get();

        // Get salary information
        $salary = $user->salary;

        // Get pending cash advances
        $pendingAdvances = $user->cashAdvances()
            ->where('status', 'pending')
            ->orderBy('request_date', 'desc')
            ->get();

        // Get recent approved cash advances
        $recentAdvances = $user->cashAdvances()
            ->where('status', 'approved')
            ->orderBy('approval_date', 'desc')
            ->take(5)
            ->get();

        // Calculate totals for current month or filtered date range
        $monthSalesQuery = $user->dailySales();

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $monthSalesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            $monthSalesQuery->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year);
        }

        $monthSales = $monthSalesQuery->sum('total_sales');

        return view('employee.dashboard', compact(
            'user',
            'recentSales',
            'salary',
            'pendingAdvances',
            'recentAdvances',
            'monthSales',
            'startDate',
            'endDate'
        ));
    }
}
