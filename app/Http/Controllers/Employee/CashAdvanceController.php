<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
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
    public function index(Request $request)
    {
        $query = CashAdvance::where('user_id', Auth::id())
            ->orderBy('request_date', 'desc');

        // Filter by status if specified
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $cashAdvances = $query->paginate(10);

        return view('employee.cash-advances.index', compact('cashAdvances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.cash-advances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Create cash advance request with pending status
        CashAdvance::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('employee.cash-advances.index')
            ->with('success', 'Permohonan kasbon berhasil dibuat dan menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cashAdvance = CashAdvance::with('approver')->findOrFail($id);

        // Only the owner can see the cash advance
        if ($cashAdvance->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('employee.cash-advances.show', compact('cashAdvance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only the owner can edit the cash advance, and only if pending
        if ($cashAdvance->user_id !== Auth::id() || $cashAdvance->status !== 'pending') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini atau permohonan sudah diproses.');
        }

        return view('employee.cash-advances.edit', compact('cashAdvance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only the owner can update the cash advance, and only if pending
        if ($cashAdvance->user_id !== Auth::id() || $cashAdvance->status !== 'pending') {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini atau permohonan sudah diproses.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Update cash advance
        $cashAdvance->update([
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('employee.cash-advances.show', $cashAdvance->id)
            ->with('success', 'Permohonan kasbon berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cashAdvance = CashAdvance::findOrFail($id);

        // Only the owner can delete the cash advance, and only if pending
        if ($cashAdvance->user_id !== Auth::id() || $cashAdvance->status !== 'pending') {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini atau permohonan sudah diproses.');
        }

        $cashAdvance->delete();

        return redirect()->route('employee.cash-advances.index')
            ->with('success', 'Permohonan kasbon berhasil dihapus.');
    }
}
