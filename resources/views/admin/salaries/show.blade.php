@extends('layouts.app')

@section('title', 'Detail Gaji Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Gaji</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%">Nama Karyawan</th>
                                <td>{{ $salary->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $salary->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Total Pendapatan</th>
                                <td>Rp {{ number_format($salary->total_earned, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Kasbon</th>
                                <td>Rp {{ number_format($salary->total_advances, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Gaji Bersih</th>
                                <td class="font-weight-bold {{ $salary->net_salary < 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ $salary->last_updated->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    @if(Auth::user()->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Gaji
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
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
                                @forelse($cashAdvances as $advance)
                                    <tr>
                                        <td>{{ $advance->request_date->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($advance->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($advance->status == 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($advance->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($advance->status == 'rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ $advance->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada riwayat kasbon</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $cashAdvances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 