@extends('layouts.app')

@section('title', 'Kelola Inventaris')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Item
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Inventaris</h6>
            <div class="btn-group">
                <a href="{{ route('admin.inventory.index', ['type' => 'raw_materials']) }}" class="btn btn-sm btn-outline-primary {{ request('type') == 'raw_materials' ? 'active' : '' }}">
                    Bahan Baku
                </a>
                <a href="{{ route('admin.inventory.index', ['type' => 'supplies']) }}" class="btn btn-sm btn-outline-primary {{ request('type') == 'supplies' ? 'active' : '' }}">
                    Perlengkapan
                </a>
                <a href="{{ route('admin.inventory.index', ['type' => 'low_stock']) }}" class="btn btn-sm btn-outline-danger {{ request('type') == 'low_stock' ? 'active' : '' }}">
                    Stok Menipis
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary {{ request('type') == null ? 'active' : '' }}">
                    Semua
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total Nilai</th>
                            <th>Batas Minimal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>
                                    @if($item->is_raw_material)
                                        <span class="badge bg-primary">Bahan Baku</span>
                                    @else
                                        <span class="badge bg-secondary">Perlengkapan</span>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ $item->reorder_point }} {{ $item->unit }}</td>
                                <td>
                                    @if($item->quantity <= $item->reorder_point)
                                        <span class="badge bg-danger">Stok Menipis</span>
                                    @else
                                        <span class="badge bg-success">Stok Cukup</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus <strong>{{ $item->item_name }}</strong> dari inventaris?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST">
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
                                <td colspan="8" class="text-center">Tidak ada data inventaris</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $inventory->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 