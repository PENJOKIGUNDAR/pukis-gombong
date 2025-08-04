<?php

namespace App\Http\Controllers;

use App\Models\CashAdvance;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvanceController extends Controller
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
        $user = Auth::user();

        // Admin can see all requests, employees only their own
        if ($user->isAdmin()) {
            $cashAdvances = CashAdvance::with(['user', 'approver'])
                ->orderBy('request_date', 'desc')
                ->paginate(10);
        } else {
            $cashAdvances = $user->cashAdvances()
                ->orderBy('request_date', 'desc')
                ->paginate(10);
        }

        return view('admin.cash-advances.index', compact('cashAdvances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = [];

        // Admin can create for any employee, employees only for themselves
        if (Auth::user()->isAdmin()) {
            $employees = User::where('role', 'employee')->get();
        }

        return view('admin.cash-advances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required_if:role,admin|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Determine user_id (admin can submit for any employee)
        $userId = Auth::user()->isAdmin() ? $request->user_id : Auth::id();

        // Create cash advance request with pending status
        CashAdvance::create([
            'user_id' => $userId,
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.cash-advances.index')
            ->with('success', 'Permohonan kasbon berhasil dibuat dan menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cashAdvance = CashAdvance::with(['user', 'approver'])->findOrFail($id);

        // Only admin or the owner can see the cash advance
        if (!Auth::user()->isAdmin() && $cashAdvance->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.cash-advances.show', compact('cashAdvance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only admin or the owner can edit the cash advance, and only if pending
        if ((!Auth::user()->isAdmin() && $cashAdvance->user_id !== Auth::id()) ||
            $cashAdvance->status !== 'pending'
        ) {
            abort(403, 'Unauthorized action or cash advance already processed.');
        }

        $employees = [];
        if (Auth::user()->isAdmin()) {
            $employees = User::where('role', 'employee')->get();
        }

        return view('admin.cash-advances.edit', compact('cashAdvance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only admin or the owner can update the cash advance, and only if pending
        if ((!Auth::user()->isAdmin() && $cashAdvance->user_id !== Auth::id()) ||
            $cashAdvance->status !== 'pending'
        ) {
            abort(403, 'Unauthorized action or cash advance already processed.');
        }

        $request->validate([
            'user_id' => 'required_if:role,admin|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Determine user_id (admin can change employee)
        $userId = Auth::user()->isAdmin() ? $request->user_id : $cashAdvance->user_id;

        // Update cash advance
        $cashAdvance->update([
            'user_id' => $userId,
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.cash-advances.index')
            ->with('success', 'Permohonan kasbon berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only admin or the owner can delete the cash advance, and only if pending
        if ((!Auth::user()->isAdmin() && $cashAdvance->user_id !== Auth::id()) ||
            $cashAdvance->status !== 'pending'
        ) {
            abort(403, 'Unauthorized action or cash advance already processed.');
        }

        $cashAdvance->delete();

        return redirect()->route('admin.cash-advances.index')
            ->with('success', 'Permohonan kasbon berhasil dihapus.');
    }

    /**
     * Approve a cash advance request.
     */
    public function approve(Request $request, string $id)
    {
        // Only admin can approve
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $cashAdvance = CashAdvance::findOrFail($id);

        // Only pending requests can be approved
        if ($cashAdvance->status !== 'pending') {
            return back()->withErrors(['message' => 'Permintaan sudah diproses sebelumnya.']);
        }

        DB::beginTransaction();

        try {
            // Update the cash advance
            $cashAdvance->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approval_date' => now(),
            ]);

            // Update the salary record
            $salary = Salary::where('user_id', $cashAdvance->user_id)->first();

            if ($salary) {
                $salary->total_advances += $cashAdvance->amount;
                $salary->net_salary = $salary->total_earned - $salary->total_advances;
                $salary->last_updated = now();
                $salary->save();
            }

            DB::commit();

            // Redirect without flashing a message - the page will show a success message
            return redirect()->route('admin.cash-advances.show', $cashAdvance->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['message' => 'Gagal menyetujui kasbon: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a cash advance request.
     */
    public function reject(Request $request, string $id)
    {
        // Only admin can reject
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $cashAdvance = CashAdvance::findOrFail($id);

        // Only pending requests can be rejected
        if ($cashAdvance->status !== 'pending') {
            return back()->withErrors(['message' => 'Permintaan sudah diproses sebelumnya.']);
        }

        // Update the cash advance
        $cashAdvance->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approval_date' => now(),
        ]);

        // Redirect without flashing a message - the page will show a rejection message
        return redirect()->route('admin.cash-advances.show', $cashAdvance->id);
    }
}
