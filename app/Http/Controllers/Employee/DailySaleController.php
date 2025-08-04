<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailySale;
use App\Models\Salary;
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

        $salesQuery = $user->dailySales()
            ->with('user')
            ->orderBy('sale_date', 'desc');

        // Filter berdasarkan tanggal jika ada
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $salesQuery->where('sale_date', '>=', $startDate);
        } elseif ($endDate) {
            $salesQuery->where('sale_date', '<=', $endDate);
        }

        $sales = $salesQuery->paginate(10);

        return view('employee.daily-sales.index', compact('sales', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Karyawan tidak dapat membuat data penjualan baru
        return redirect()->route('employee.daily-sales.index')
            ->with('error', 'Maaf, hanya admin yang dapat menginput penjualan harian. Silakan hubungi admin untuk menginput data penjualan.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Karyawan tidak dapat membuat data penjualan baru
        return redirect()->route('employee.daily-sales.index')
            ->with('error', 'Maaf, hanya admin yang dapat menginput penjualan harian. Silakan hubungi admin untuk menginput data penjualan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sale = DailySale::with('user')->findOrFail($id);

        // Only the owner can see the sale
        if ($sale->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('employee.daily-sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Karyawan tidak dapat mengubah data penjualan
        return redirect()->route('employee.daily-sales.index')
            ->with('error', 'Maaf, hanya admin yang dapat mengubah data penjualan harian. Silakan hubungi admin untuk mengubah data penjualan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Karyawan tidak dapat mengubah data penjualan
        return redirect()->route('employee.daily-sales.index')
            ->with('error', 'Maaf, hanya admin yang dapat mengubah data penjualan harian. Silakan hubungi admin untuk mengubah data penjualan.');
    }
}
