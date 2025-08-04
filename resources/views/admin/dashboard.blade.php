@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Data</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Summary Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Penjualan Hari Ini</h6>
                        <h4 class="mb-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h4>
                    </div>
                    <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Penjualan {{ request('start_date') || request('end_date') ? 'Periode' : 'Bulanan' }}</h6>
                        <h4 class="mb-0">Rp {{ number_format($monthSales, 0, ',', '.') }}</h4>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Karyawan Aktif</h6>
                        <h4 class="mb-0">{{ $employees->count() }}</h4>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Kasbon Tertunda</h6>
                        <h4 class="mb-0">{{ $pendingAdvances->count() }}</h4>
                    </div>
                    <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Sales Table -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Penjualan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Adonan (KG)</th>
                                <th>Sisa Belum Tercetak (pcs)</th>
                                <th>Kue Tidak Terjual</th>
                                <th>Total Penjualan</th>
                                <th>Bagian Admin</th>
                                <th>Bagian Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td>{{ $sale->dough_brought }}</td>
                                    <td>{{ $sale->dough_remaining_unprinted }}</td>
                                    <td>{{ $sale->dough_remaining_printed }}</td>
                                    <td>Rp {{ number_format($sale->total_sales, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->admin_share, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->employee_share, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">Tidak ada penjualan terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('admin.daily-sales.index') }}" class="btn btn-sm btn-primary">Lihat Semua Penjualan</a>
            </div>
        </div>
    </div>
    
    <!-- Pending Advances and Low Stock -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Kasbon Tertunda</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendingAdvances as $advance)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ $advance->user->name }}</span>
                                <br>
                                <small class="text-muted">Rp {{ number_format($advance->amount, 0, ',', '.') }}</small>
                            </div>
                            <a href="{{ route('admin.cash-advances.show', $advance->id) }}" class="btn btn-sm btn-outline-primary">Tinjau</a>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-3">Tidak ada kasbon tertunda</li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('admin.cash-advances.index') }}" class="btn btn-sm btn-primary">Lihat Semua Kasbon</a>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Stok Menipis</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockItems as $item)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">{{ $item->item_name }}</span>
                                <span class="badge bg-danger">Rendah</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">{{ $item->quantity }} {{ $item->unit }} tersedia</small>
                                <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Tambah Stok</a>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-3">Tidak ada item dengan stok rendah</li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-primary">Lihat Inventaris</a>
            </div>
        </div>
    </div>
</div>
@endsection 