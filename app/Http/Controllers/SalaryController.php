<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use App\Models\CashAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only admin can see all salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $salaries = Salary::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'employee');
            })
            ->get();

        return view('admin.salaries.index', compact('salaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only admin can create salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Get employees without salary records
        $employees = User::where('role', 'employee')
            ->whereDoesntHave('salary')
            ->get();

        if ($employees->isEmpty()) {
            return redirect()->route('admin.salaries.index')
                ->with('info', 'All employees already have salary records.');
        }

        return view('admin.salaries.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can store salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_earned' => 'required|numeric|min:0',
            'total_advances' => 'required|numeric|min:0',
        ]);

        // Calculate net salary
        $netSalary = $request->total_earned - $request->total_advances;

        // Create salary record
        Salary::create([
            'user_id' => $request->user_id,
            'total_earned' => $request->total_earned,
            'total_advances' => $request->total_advances,
            'net_salary' => $netSalary,
            'last_updated' => now(),
        ]);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        // Admin can see any salary, employee only their own
        if ($user->isAdmin()) {
            $salary = Salary::with('user')->findOrFail($id);
        } else {
            // For employees, id parameter is ignored, show their own salary
            $salary = $user->salary;

            if (!$salary) {
                abort(404, 'Salary record not found.');
            }
        }

        // Get the cash advances for this user
        $cashAdvances = CashAdvance::where('user_id', $salary->user_id)
            ->orderBy('request_date', 'desc')
            ->paginate(10);

        return view('admin.salaries.show', compact('salary', 'cashAdvances'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Only admin can edit salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $salary = Salary::with('user')->findOrFail($id);

        return view('admin.salaries.edit', compact('salary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only admin can update salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'total_earned' => 'required|numeric|min:0',
            'total_advances' => 'required|numeric|min:0',
        ]);

        $salary = Salary::findOrFail($id);

        // Calculate net salary
        $netSalary = $request->total_earned - $request->total_advances;

        // Update salary record
        $salary->update([
            'total_earned' => $request->total_earned,
            'total_advances' => $request->total_advances,
            'net_salary' => $netSalary,
            'last_updated' => now(),
        ]);

        return redirect()->route('admin.salaries.show', $salary->id)
            ->with('success', 'Salary record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only admin can delete salaries
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $salary = Salary::findOrFail($id);

        // Only delete if there are no related records
        if ($salary->cashAdvances()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete salary record with associated cash advances.']);
        }

        $salary->delete();

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record deleted successfully.');
    }
}
