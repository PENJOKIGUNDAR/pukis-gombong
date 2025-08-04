@extends('layouts.app')

@section('title', 'Tambah Item Inventaris')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Item</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Nama Item</label>
                            <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                                id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                            @error('item_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="is_raw_material" class="form-label">Kategori</label>
                                <select class="form-select @error('is_raw_material') is-invalid @enderror" 
                                    id="is_raw_material" name="is_raw_material" required>
                                    <option value="1" {{ old('is_raw_material') == '1' ? 'selected' : '' }}>Bahan Baku</option>
                                    <option value="0" {{ old('is_raw_material') == '0' ? 'selected' : '' }}>Perlengkapan</option>
                                </select>
                                @error('is_raw_material')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Satuan</label>
                                <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                    id="unit" name="unit" value="{{ old('unit') }}" required 
                                    placeholder="kg, pcs, liter, dll">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Jumlah Stok Awal</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                    id="quantity" name="quantity" value="{{ old('quantity', 0) }}" required 
                                    step="0.01">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="unit_price" class="form-label">Harga per Satuan (Rp)</label>
                                <input type="number" class="form-control @error('unit_price') is-invalid @enderror" 
                                    id="unit_price" name="unit_price" value="{{ old('unit_price', 0) }}" required>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="reorder_point" class="form-label">Batas Minimal Stok</label>
                                <input type="number" class="form-control @error('reorder_point') is-invalid @enderror" 
                                    id="reorder_point" name="reorder_point" value="{{ old('reorder_point', 0) }}" required 
                                    step="0.01">
                                @error('reorder_point')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Jumlah minimum sebelum harus melakukan pembelian lagi</small>
                            </div>
                            <div class="col-md-6">
                                <label for="last_restock_date" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control @error('last_restock_date') is-invalid @enderror" 
                                    id="last_restock_date" name="last_restock_date" value="{{ old('last_restock_date', date('Y-m-d')) }}" required>
                                @error('last_restock_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 