@extends('layouts.app')

@section('title', 'Detail Kasbon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <a href="{{ route('admin.cash-advances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($cashAdvance->status == 'approved' && !session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Permohonan kasbon berhasil disetujui.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($cashAdvance->status == 'rejected' && !session('success'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Permohonan kasbon telah ditolak.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Kasbon</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">ID Kasbon</th>
                                <td># {{ $cashAdvance->id }}</td>
                            </tr>
                            <tr>
                                <th>Nama Karyawan</th>
                                <td>{{ $cashAdvance->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Kasbon</th>
                                <td class="font-weight-bold">Rp {{ number_format($cashAdvance->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <td>{{ $cashAdvance->request_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($cashAdvance->status == 'pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($cashAdvance->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($cashAdvance->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @if($cashAdvance->status != 'pending')
                            <tr>
                                <th>Diproses Oleh</th>
                                <td>{{ $cashAdvance->approver->name }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Diproses</th>
                                <td>{{ $cashAdvance->approval_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Catatan</th>
                                <td>{{ $cashAdvance->notes ?? 'Tidak ada catatan' }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($cashAdvance->status == 'pending' && Auth::user()->isAdmin())
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('admin.cash-advances.approve', $cashAdvance->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check"></i> Setujui Kasbon
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.cash-advances.reject', $cashAdvance->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times"></i> Tolak Kasbon
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cashAdvance->status == 'pending')
                    <div class="mt-3">
                        <a href="{{ route('admin.cash-advances.edit', $cashAdvance->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Kasbon
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Hapus Kasbon
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus permintaan kasbon sebesar 
                                        <strong>Rp {{ number_format($cashAdvance->amount, 0, ',', '.') }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.cash-advances.destroy', $cashAdvance->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 