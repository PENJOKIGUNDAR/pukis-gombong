@extends('layouts.app')

@section('title', 'Detail Inventaris')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Item</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Nama Item</th>
                                <td>{{ $item->item_name }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>
                                    @if($item->is_raw_material)
                                        <span class="badge bg-primary">Bahan Baku</span>
                                    @else
                                        <span class="badge bg-secondary">Perlengkapan</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $item->description ?? 'Tidak ada deskripsi' }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Stok</th>
                                <td>
                                    {{ $item->quantity }} {{ $item->unit }}
                                    @if($item->quantity <= $item->reorder_point)
                                        <span class="badge bg-danger ms-2">Stok Menipis</span>
                                    @else
                                        <span class="badge bg-success ms-2">Stok Cukup</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Satuan</th>
                                <td>{{ $item->unit }}</td>
                            </tr>
                            <tr>
                                <th>Harga per Satuan</th>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Nilai</th>
                                <td>Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Batas Minimal Stok</th>
                                <td>{{ $item->reorder_point }} {{ $item->unit }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ $item->last_restock_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Ditambahkan Oleh</th>
                                <td>{{ $item->addedBy->name }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-flex mt-3">
                        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Hapus Item
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
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Stok</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.update-stock', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah Stok Terbaru</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}" required step="0.01">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan jumlah stok saat ini</small>
                        </div>

                        <div class="mb-3">
                            <label for="last_restock_date" class="form-label">Tanggal Update</label>
                            <input type="date" class="form-control @error('last_restock_date') is-invalid @enderror" 
                                id="last_restock_date" name="last_restock_date" value="{{ old('last_restock_date', date('Y-m-d')) }}" required>
                            @error('last_restock_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Update Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 