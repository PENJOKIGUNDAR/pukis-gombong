<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailySaleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter tanggal
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        // Filter karyawan (hanya untuk admin)
        $employeeId = $request->input('employee_id');

        // Admin can see all sales, employees only their own
        if ($user->isAdmin()) {
            $salesQuery = DailySale::with('user')
                ->orderBy('sale_date', 'desc');

            // Filter berdasarkan karyawan jika dipilih
            if ($employeeId) {
                $salesQuery->where('user_id', $employeeId);
            }
        } else {
            $salesQuery = $user->dailySales()
                ->orderBy('sale_date', 'desc');
        }

        // Filter berdasarkan tanggal jika ada
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $salesQuery->where('sale_date', '>=', $startDate);
        } elseif ($endDate) {
            $salesQuery->where('sale_date', '<=', $endDate);
        }

        $sales = $salesQuery->paginate(10);

        // Data untuk filter dropdown karyawan
        $employees = [];
        if ($user->isAdmin()) {
            $employees = User::where('role', 'employee')->get();
        }

        return view('admin.daily-sales.index', compact('sales', 'employees', 'startDate', 'endDate', 'employeeId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only allow admin to create sales records
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('admin.daily-sales.index')
                ->with('error', 'Hanya Admin yang dapat menginput penjualan harian.');
        }

        $employees = User::where('role', 'employee')->get();

        return view('admin.daily-sales.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only allow admin to create sales records
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('admin.daily-sales.index')
                ->with('error', 'Hanya Admin yang dapat menginput penjualan harian.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sale_date' => 'required|date',
            'dough_brought' => 'required|numeric|min:0.1',
            'dough_remaining_printed' => 'required|integer|min:0',
            'dough_remaining_unprinted' => 'required|integer|min:0',
            'total_sales' => 'required|numeric|min:0',
            'employee_expenses' => 'nullable|numeric|min:0',
            'unsold_pastries' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Determine user_id
        $userId = $request->user_id;

        // Calculate shares (80% for admin, 20% for employee)
        $employeeExpenses = $request->employee_expenses ?? 0;
        $adminShare = $request->total_sales * 0.8;
        $employeeShare = ($request->total_sales * 0.2) - $employeeExpenses;

        DB::beginTransaction();

        try {
            // Create the daily sale record
            // Admin creates records, so they're auto-verified
            $sale = DailySale::create([
                'user_id' => $userId,
                'sale_date' => $request->sale_date,
                'dough_brought' => $request->dough_brought,
                'dough_remaining_printed' => $request->dough_remaining_printed,
                'dough_remaining_unprinted' => $request->dough_remaining_unprinted,
                'total_sales' => $request->total_sales,
                'admin_share' => $adminShare,
                'employee_share' => $employeeShare,
                'employee_expenses' => $employeeExpenses,
                'unsold_pastries' => $request->unsold_pastries ?? $request->dough_remaining_printed,
                'notes' => $request->notes,
                'is_verified' => true,
            ]);

            // Update the employee's salary
            $salary = Salary::where('user_id', $userId)->first();

            if ($salary) {
                $salary->total_earned += $employeeShare;
                $salary->net_salary += $employeeShare;
                $salary->last_updated = now();
                $salary->save();
            }

            DB::commit();

            return redirect()->route('admin.daily-sales.index')
                ->with('success', 'Penjualan harian berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Gagal mencatat penjualan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sale = DailySale::with('user')->findOrFail($id);

        // Only admin or the owner can see the sale
        if (!Auth::user()->isAdmin() && $sale->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.daily-sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sale = DailySale::findOrFail($id);

        // Only admin or the owner can edit the sale
        if (!Auth::user()->isAdmin() && $sale->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $employees = [];
        if (Auth::user()->isAdmin()) {
            $employees = User::where('role', 'employee')->get();
        }

        return view('admin.daily-sales.edit', compact('sale', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only allow admin to update sales records
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('admin.daily-sales.index')
                ->with('error', 'Hanya Admin yang dapat mengubah data penjualan harian.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sale_date' => 'required|date',
            'dough_brought' => 'required|numeric|min:0.1',
            'dough_remaining_printed' => 'required|integer|min:0',
            'dough_remaining_unprinted' => 'required|integer|min:0',
            'total_sales' => 'required|numeric|min:0',
            'employee_expenses' => 'nullable|numeric|min:0',
            'unsold_pastries' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $sale = DailySale::findOrFail($id);

        // Determine user_id
        $userId = $request->user_id;

        // Calculate shares (80% for admin, 20% for employee)
        $employeeExpenses = $request->employee_expenses ?? 0;
        $adminShare = $request->total_sales * 0.8;
        $employeeShare = ($request->total_sales * 0.2) - $employeeExpenses;

        DB::beginTransaction();

        try {
            // Get old values for salary adjustment
            $oldEmployeeShare = $sale->employee_share;
            $oldUserId = $sale->user_id;
            $oldIsVerified = $sale->is_verified;

            // Update the daily sale record
            $wasVerified = $sale->is_verified;
            $sale->update([
                'user_id' => $userId,
                'sale_date' => $request->sale_date,
                'dough_brought' => $request->dough_brought,
                'dough_remaining_printed' => $request->dough_remaining_printed,
                'dough_remaining_unprinted' => $request->dough_remaining_unprinted,
                'total_sales' => $request->total_sales,
                'admin_share' => $adminShare,
                'employee_share' => $employeeShare,
                'employee_expenses' => $employeeExpenses,
                'notes' => $request->notes,
                'is_verified' => true,
                'unsold_pastries' => $request->unsold_pastries ?? $request->dough_remaining_printed,
            ]);

            // Handle salary adjustments if the sale was previously verified
            if ($oldIsVerified) {
                // Revert the old salary adjustment if verified
                $oldSalary = Salary::where('user_id', $oldUserId)->first();
                if ($oldSalary) {
                    $oldSalary->total_earned -= $oldEmployeeShare;
                    $oldSalary->net_salary -= $oldEmployeeShare;
                    $oldSalary->last_updated = now();
                    $oldSalary->save();
                }

                // Add the new adjustment
                $newSalary = Salary::where('user_id', $userId)->first();
                if ($newSalary) {
                    $newSalary->total_earned += $employeeShare;
                    $newSalary->net_salary += $employeeShare;
                    $newSalary->last_updated = now();
                    $newSalary->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.daily-sales.show', $id)
                ->with('success', 'Data penjualan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Gagal memperbarui data penjualan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = DailySale::findOrFail($id);

        // Only admin can delete sales
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // Get employee share to update salary if the sale is verified
            if ($sale->is_verified) {
                $employeeShare = $sale->employee_share;
                $userId = $sale->user_id;

                // Update the employee's salary
                $salary = Salary::where('user_id', $userId)->first();
                if ($salary) {
                    $salary->total_earned -= $employeeShare;
                    $salary->net_salary -= $employeeShare;
                    $salary->last_updated = now();
                    $salary->save();
                }
            }

            // Delete the sale
            $sale->delete();

            DB::commit();

            return redirect()->route('admin.daily-sales.index')
                ->with('success', 'Daily sales deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Failed to delete sales: ' . $e->getMessage()]);
        }
    }

    /**
     * Verify a daily sale record.
     */
    public function verify(string $id)
    {
        // Only admin can verify sales
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $sale = DailySale::findOrFail($id);

        if ($sale->is_verified) {
            return redirect()->route('admin.daily-sales.show', $sale->id)
                ->with('info', 'This sales record is already verified.');
        }

        DB::beginTransaction();

        try {
            // Update verification status
            $sale->is_verified = true;
            $sale->save();

            // Update employee salary
            $salary = Salary::where('user_id', $sale->user_id)->first();
            if ($salary) {
                $salary->total_earned += $sale->employee_share;
                $salary->net_salary += $sale->employee_share;
                $salary->last_updated = now();
                $salary->save();
            }

            DB::commit();

            return redirect()->route('admin.daily-sales.show', $sale->id)
                ->with('success', 'Sales record verified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Failed to verify sales: ' . $e->getMessage()]);
        }
    }

    /**
     * Unverify a daily sale record.
     */
    public function unverify(string $id)
    {
        // Only admin can unverify sales
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $sale = DailySale::findOrFail($id);

        if (!$sale->is_verified) {
            return redirect()->route('admin.daily-sales.show', $sale->id)
                ->with('info', 'This sales record is already unverified.');
        }

        DB::beginTransaction();

        try {
            // Update verification status
            $sale->is_verified = false;
            $sale->save();

            // Update employee salary (deduct the share)
            $salary = Salary::where('user_id', $sale->user_id)->first();
            if ($salary) {
                $salary->total_earned -= $sale->employee_share;
                $salary->net_salary -= $sale->employee_share;
                $salary->last_updated = now();
                $salary->save();
            }

            DB::commit();

            return redirect()->route('admin.daily-sales.show', $sale->id)
                ->with('success', 'Sales record unverified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Failed to unverify sales: ' . $e->getMessage()]);
        }
    }
}
