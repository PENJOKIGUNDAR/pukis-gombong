@extends('layouts.app')

@section('title', 'Detail Penjualan Harian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('employee.daily-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Penjualan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 25%">Tanggal Penjualan</th>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $sale->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Adonan Dibawa</th>
                        <td>{{ $sale->dough_brought }} KG (Nilai: Rp {{ number_format($sale->dough_brought * 198000, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <th>Sisa Adonan Belum Tercetak</th>
                        <td>{{ $sale->dough_remaining_unprinted }} pcs (Nilai: Rp {{ number_format($sale->dough_remaining_unprinted * 1000, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <th>Adonan Terjual</th>
                        <td>{{ number_format(($sale->dough_brought * 200 - $sale->dough_remaining_unprinted - $sale->dough_remaining_printed) / 200, 2) }} KG (Nilai: Rp {{ number_format(($sale->dough_brought * 200 - $sale->dough_remaining_unprinted - $sale->dough_remaining_printed) * 1000, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <th>Jumlah Kue Tidak Terjual</th>
                        <td>{{ $sale->dough_remaining_printed }} kue (Nilai: Rp {{ number_format($sale->dough_remaining_printed * 1000, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <th>Total Penjualan</th>
                        <td>Rp {{ number_format($sale->total_sales, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Uang Terpakai oleh Karyawan</th>
                        <td>Rp {{ number_format($sale->employee_expenses ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Bagian Admin/Owner (80%)</th>
                        <td>Rp {{ number_format($sale->admin_share, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Bagian Karyawan (20% - pengeluaran)</th>
                        <td>Rp {{ number_format($sale->employee_share, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $sale->notes ?? 'Tidak ada catatan' }}</td>
                    </tr>
                    <tr>
                        <th>Status Verifikasi</th>
                        <td>
                            @if($sale->is_verified)
                                <span class="badge bg-success">Terverifikasi</span>
                            @else
                                <span class="badge bg-warning">Belum Diverifikasi</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="alert alert-info mt-4">
                <div class="fw-bold">Informasi Perhitungan:</div>
                <ul class="mb-0">
                    <li>1 KG adonan = 198 pcs kue</li>
                    <li>1 teko ≈ Rp 66.000</li>
                    <li>1 kue = Rp 1.000</li>
                    <li>Bagian Admin/Owner: 80% dari total penjualan</li>
                    <li>Bagian Karyawan: 20% dari total penjualan (dikurangi uang yang terpakai)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 