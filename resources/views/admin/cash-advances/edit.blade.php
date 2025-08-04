@extends('layouts.app')

@section('title', 'Edit Kasbon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <a href="{{ route('admin.cash-advances.show', $cashAdvance->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Kasbon</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cash-advances.update', $cashAdvance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @if(Auth::user()->isAdmin())
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Karyawan</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('user_id', $cashAdvance->user_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <div class="mb-3">
                            <label class="form-label">Karyawan</label>
                            <input type="text" class="form-control" value="{{ $cashAdvance->user->name }}" readonly>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Kasbon (Rp)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                id="amount" name="amount" value="{{ old('amount', $cashAdvance->amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="request_date" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control @error('request_date') is-invalid @enderror" 
                                id="request_date" name="request_date" value="{{ old('request_date', $cashAdvance->request_date->format('Y-m-d')) }}" required>
                            @error('request_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes', $cashAdvance->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Perubahan pada permohonan kasbon akan memerlukan persetujuan ulang.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.cash-advances.show', $cashAdvance->id) }}" class="btn btn-light me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 