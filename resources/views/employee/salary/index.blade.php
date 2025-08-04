@extends('layouts.app')

@section('title', 'Data Gaji Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Ringkasan Gaji</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle mx-auto d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-money-bill-wave fa-4x text-primary"></i>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <h5 class="font-weight-bold">Total Gaji Bersih</h5>
                        <h2 class="text-success mb-4">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</h2>
                    </div>
                    
                    <div class="list-group mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Pendapatan</span>
                            <span class="badge bg-primary">Rp {{ number_format($salary->total_earned, 0, ',', '.') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Kasbon</span>
                            <span class="badge bg-warning">Rp {{ number_format($salary->total_advances, 0, ',', '.') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Gaji Bersih</span>
                            <span class="badge bg-success">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="text-center text-muted">
                        <small>Terakhir diperbarui: {{ $salary->last_updated->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Penjualan & Pendapatan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Penjualan</th>
                                    <th>Bagian Saya (20%)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesHistory as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($sale->total_sales, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($sale->employee_share, 0, ',', '.') }}</td>
                                        <td>
                                            @if($sale->is_verified)
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data penjualan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="1" class="text-end">Total Bulan Ini:</th>
                                    <th>Rp {{ number_format($monthlySales, 0, ',', '.') }}</th>
                                    <th>Rp {{ number_format($monthlyEarnings, 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        {{ $salesHistory->links() }}
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Kasbon</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advancesHistory as $advance)
                                    <tr>
                                        <td>{{ $advance->request_date->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($advance->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($advance->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($advance->status == 'rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>{{ $advance->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data kasbon</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        {{ $advancesHistory->links() }}
                        <a href="{{ route('employee.cash-advances.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajukan Kasbon
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 