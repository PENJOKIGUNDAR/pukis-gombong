@extends('layouts.app')

@section('title', 'Kelola Penjualan Harian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Riwayat Penjualan Saya</h4>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('employee.daily-sales.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" 
                        value="{{ request('start_date') ?? ($startDate ? $startDate->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" 
                        value="{{ request('end_date') ?? ($endDate ? $endDate->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('employee.daily-sales.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penjualan Harian</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Adonan (KG)</th>
                            <th>Sisa Belum Tercetak (pcs)</th>
                            <th>Kue Tidak Terjual</th>
                            <th>Total Penjualan</th>
                            <th>Pengeluaran Karyawan</th>
                            <th>Bagian Karyawan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td>{{ $sale->dough_brought }}</td>
                                <td>{{ $sale->dough_remaining_unprinted }}</td>
                                <td>{{ $sale->dough_remaining_printed }}</td>
                                <td>Rp {{ number_format($sale->total_sales, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->employee_expenses ?? 0, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->employee_share, 0, ',', '.') }}</td>
                                <td>
                                    @if($sale->is_verified)
                                        <span class="badge bg-success">Terverifikasi</span>
                                    @else
                                        <span class="badge bg-warning">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('employee.daily-sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="5" class="text-end">Total:</th>
                            <th>Rp {{ number_format($sales->sum('total_sales'), 0, ',', '.') }}</th>
                            <th>Rp {{ number_format($sales->sum('employee_expenses'), 0, ',', '.') }}</th>
                            <th>Rp {{ number_format($sales->sum('employee_share'), 0, ',', '.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $sales->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 