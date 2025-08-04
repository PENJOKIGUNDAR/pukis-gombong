@extends('layouts.app')

@section('title', 'Edit Data Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Gaji</h1>
        <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Data Gaji</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.salaries.update', $salary->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Karyawan</label>
                    <input type="text" class="form-control" value="{{ $salary->user->name }}" readonly>
                    <small class="form-text text-muted">Karyawan tidak dapat diubah pada data gaji yang sudah ada.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_earned" class="form-label">Total Pendapatan (Rp)</label>
                            <input type="number" class="form-control @error('total_earned') is-invalid @enderror" 
                                id="total_earned" name="total_earned" value="{{ old('total_earned', $salary->total_earned) }}" required>
                            @error('total_earned')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Pendapatan akan bertambah otomatis dari penjualan harian.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_advances" class="form-label">Total Kasbon (Rp)</label>
                            <input type="number" class="form-control @error('total_advances') is-invalid @enderror" 
                                id="total_advances" name="total_advances" value="{{ old('total_advances', $salary->total_advances) }}" required>
                            @error('total_advances')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Kasbon akan bertambah otomatis saat permintaan disetujui.</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gaji Bersih (Rp)</label>
                    <div class="form-control bg-light" id="net_salary_display">
                        Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
                    </div>
                    <small class="form-text text-muted">Gaji bersih = Total Pendapatan - Total Kasbon</small>
                </div>

                <div class="alert alert-warning">
                    <div class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Peringatan:</div>
                    <p class="mb-0">Mengubah data gaji secara manual dapat mempengaruhi konsistensi data. 
                    Pastikan perubahan yang dilakukan sesuai dengan data sebenarnya.</p>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn btn-light me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Auto-calculate net salary
    document.addEventListener('DOMContentLoaded', function() {
        const totalEarnedInput = document.getElementById('total_earned');
        const totalAdvancesInput = document.getElementById('total_advances');
        const netSalaryDisplay = document.getElementById('net_salary_display');

        function calculateNetSalary() {
            const totalEarned = parseFloat(totalEarnedInput.value) || 0;
            const totalAdvances = parseFloat(totalAdvancesInput.value) || 0;
            const netSalary = totalEarned - totalAdvances;
            
            // Format as IDR
            netSalaryDisplay.textContent = 'Rp ' + netSalary.toLocaleString('id-ID');
            
            // Change color based on value
            if (netSalary < 0) {
                netSalaryDisplay.classList.add('text-danger');
                netSalaryDisplay.classList.remove('text-success');
            } else {
                netSalaryDisplay.classList.add('text-success');
                netSalaryDisplay.classList.remove('text-danger');
            }
        }

        // Calculate initially and on any input change
        calculateNetSalary();
        totalEarnedInput.addEventListener('input', calculateNetSalary);
        totalAdvancesInput.addEventListener('input', calculateNetSalary);
    });
</script>
@endsection

@endsection 