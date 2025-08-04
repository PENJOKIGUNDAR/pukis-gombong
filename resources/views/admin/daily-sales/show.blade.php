@extends('layouts.app')

@section('title', 'Detail Penjualan Harian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.daily-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

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
                        <td>{{ $sale->dough_brought }} KG (Perkiraan: Rp {{ number_format($sale->dough_brought * 198000, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <th>Sisa Adonan (Belum Dicetak)</th>
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

            <div class="mt-4 d-flex">
                <a href="{{ route('admin.daily-sales.edit', $sale->id) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                @if($sale->is_verified)
                    <form action="{{ route('admin.daily-sales.unverify', $sale->id) }}" method="POST" class="me-2">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-times-circle"></i> Batalkan Verifikasi
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.daily-sales.verify', $sale->id) }}" method="POST" class="me-2">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Verifikasi
                        </button>
                    </form>
                @endif
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus data penjualan pada tanggal {{ $sale->sale_date->format('d M Y') }}?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('admin.daily-sales.destroy', $sale->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 