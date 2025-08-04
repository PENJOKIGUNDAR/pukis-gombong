@extends('layouts.app')

@section('title', 'Riwayat Kasbon Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('employee.cash-advances.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajukan Kasbon
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kasbon Saya</h6>
            
            <div class="btn-group">
                <a href="{{ route('employee.cash-advances.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                    Menunggu
                </a>
                <a href="{{ route('employee.cash-advances.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">
                    Disetujui
                </a>
                <a href="{{ route('employee.cash-advances.index', ['status' => 'rejected']) }}" class="btn btn-sm btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                    Ditolak
                </a>
                <a href="{{ route('employee.cash-advances.index') }}" class="btn btn-sm btn-outline-primary {{ request('status') == null ? 'active' : '' }}">
                    Semua
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Diproses Pada</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
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
                                <td>
                                    @if($advance->approval_date)
                                        {{ $advance->approval_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $advance->notes ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('employee.cash-advances.show', $advance->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($advance->status == 'pending')
                                            <a href="{{ route('employee.cash-advances.edit', $advance->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $advance->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    @if($advance->status == 'pending')
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $advance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus permintaan kasbon sebesar
                                                    <strong>Rp {{ number_format($advance->amount, 0, ',', '.') }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('employee.cash-advances.destroy', $advance->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kasbon</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $cashAdvances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 