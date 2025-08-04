@extends('layouts.app')

@section('title', 'Ajukan Kasbon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
   
        <a href="{{ route('employee.cash-advances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Kasbon</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.cash-advances.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Kasbon (Rp)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                id="amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Jumlah maksimal kasbon adalah total gaji yang belum dibayarkan.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="request_date" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control @error('request_date') is-invalid @enderror" 
                                id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required>
                            @error('request_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Berikan alasan pengajuan kasbon untuk mempermudah proses persetujuan.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <p class="mb-0">Informasi Penting:</p>
                                    <ul class="mb-0">
                                        <li>Pengajuan kasbon akan diproses oleh admin</li>
                                        <li>Kasbon yang disetujui akan dipotong dari gaji Anda</li>
                                        <li>Status pengajuan dapat dilihat di halaman Riwayat Kasbon</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Ajukan Kasbon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 