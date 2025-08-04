@extends('layouts.app')

@section('title', 'Kelola Penjualan Harian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.daily-sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Penjualan
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.daily-sales.index') }}" class="row g-3">
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
                <div class="col-md-3">
                    <label for="employee_id" class="form-label">Karyawan</label>
                    <select id="employee_id" name="employee_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.daily-sales.index') }}" class="btn btn-secondary">Reset</a>
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
                            <th>Karyawan</th>
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
                                <td>{{ $sale->user->name }}</td>
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
                                        <a href="{{ route('admin.daily-sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.daily-sales.edit', $sale->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $sale->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
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