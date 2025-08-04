@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Data</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employee.dashboard') }}" method="GET" class="row g-3">
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
                        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Employee Information Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informasi Karyawan</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light rounded-circle mx-auto d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-4x text-primary"></i>
                    </div>
                </div>
                <h4 class="text-center">{{ $user->name }}</h4>
                <p class="text-center text-muted mb-4">{{ $user->email }}</p>
                
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-money-bill-wave me-2"></i> Gaji Saat Ini</span>
                        <span class="badge bg-success rounded-pill">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-line me-2"></i> Total Penghasilan</span>
                        <span class="badge bg-primary rounded-pill">Rp {{ number_format($salary->total_earned, 0, ',', '.') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-hand-holding-usd me-2"></i> Total Kasbon</span>
                        <span class="badge bg-warning rounded-pill">Rp {{ number_format($salary->total_advances, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('employee.salary.show') }}" class="btn btn-primary w-100">Lihat Detail Gaji</a>
            </div>
        </div>
    </div>
    
    <!-- Sales Summary -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Ringkasan Penjualan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Adonan (KG)</th>
                                <th>Sisa Belum Tercetak (pcs)</th>
                                <th>Kue Tidak Terjual</th>
                                <th>Total Penjualan</th>
                                <th>Bagian Saya</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>{{ $sale->dough_brought }}</td>
                                    <td>{{ $sale->dough_remaining_unprinted }}</td>
                                    <td>{{ $sale->dough_remaining_printed }}</td>
                                    <td>Rp {{ number_format($sale->total_sales, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->employee_share, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('employee.daily-sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada penjualan terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('employee.daily-sales.index') }}" class="btn btn-sm btn-primary">Lihat Semua Penjualan</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Performance -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Kinerja Bulanan</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h6 class="text-muted">{{ request('start_date') || request('end_date') ? 'Penjualan Periode' : 'Penjualan Bulan Ini' }}</h6>
                    <h2 class="display-5 fw-bold text-primary">Rp {{ number_format($monthSales, 0, ',', '.') }}</h2>
                </div>
                
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">70% Terjual</div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Pertahankan kerja bagus! Kinerja Anda hebat bulan ini.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cash Advances -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Kasbon</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendingAdvances as $advance)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">Rp {{ number_format($advance->amount, 0, ',', '.') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $advance->request_date->format('d M Y') }}</small>
                                </div>
                                <span class="badge bg-warning">Menunggu</span>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center">Tidak ada kasbon tertunda</li>
                    @endforelse
                    
                    @foreach($recentAdvances as $advance)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">Rp {{ number_format($advance->amount, 0, ',', '.') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $advance->approval_date->format('d M Y') }}</small>
                                </div>
                                <span class="badge bg-success">Disetujui</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('employee.cash-advances.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                    <a href="{{ route('employee.cash-advances.create') }}" class="btn btn-sm btn-success">Ajukan Kasbon</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 