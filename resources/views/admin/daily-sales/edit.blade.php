@extends('layouts.app')

@section('title', 'Edit Penjualan Harian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.daily-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Penjualan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.daily-sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Karyawan</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('user_id', $sale->user_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sale_date" class="form-label">Tanggal Penjualan</label>
                            <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                                id="sale_date" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dough_brought" class="form-label">Jumlah Adonan Dibawa (KG)</label>
                            <input type="number" step="0.01" class="form-control @error('dough_brought') is-invalid @enderror" 
                                id="dough_brought" name="dough_brought" value="{{ old('dough_brought', $sale->dough_brought) }}" required>
                            @error('dough_brought')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dough_remaining_unprinted" class="form-label">Sisa Adonan Belum Tercetak (pcs)</label>
                            <input type="number" class="form-control @error('dough_remaining_unprinted') is-invalid @enderror" 
                                id="dough_remaining_unprinted" name="dough_remaining_unprinted" value="{{ old('dough_remaining_unprinted', $sale->dough_remaining_unprinted) }}" required>
                            <small class="text-muted">Jumlah kue yang belum dicetak (1 kg adonan = 198 pcs)</small>
                            @error('dough_remaining_unprinted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dough_remaining_printed" class="form-label">Jumlah Kue Tidak Terjual (pcs)</label>
                            <input type="number" class="form-control @error('dough_remaining_printed') is-invalid @enderror" 
                                id="dough_remaining_printed" name="dough_remaining_printed" value="{{ old('dough_remaining_printed', $sale->dough_remaining_printed) }}" required>
                            <small class="text-muted">Jumlah kue yang sudah dicetak namun tidak terjual (1 kue = Rp 1.000)</small>
                            @error('dough_remaining_printed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="employee_expenses" class="form-label">Uang Terpakai oleh Karyawan (Rp)</label>
                            <input type="number" class="form-control @error('employee_expenses') is-invalid @enderror" 
                                id="employee_expenses" name="employee_expenses" value="{{ old('employee_expenses', $sale->employee_expenses ?? 0) }}">
                            <small class="text-muted">Uang yang dipakai karyawan selama berjualan (dipotong dari bagian karyawan)</small>
                            @error('employee_expenses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="total_sales" class="form-label">Total Penjualan (Rp)</label>
                            <input type="number" class="form-control @error('total_sales') is-invalid @enderror" 
                                id="total_sales" name="total_sales" value="{{ old('total_sales', $sale->total_sales) }}" required>
                            @error('total_sales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                        id="notes" name="notes" rows="3">{{ old('notes', $sale->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <div class="fw-bold">Informasi Perhitungan:</div>
                    <ul class="mb-2">
                        <li>1 KG adonan = 198 pcs kue</li>
                        <li>1 teko ≈ Rp 66.000</li>
                        <li>1 kue = Rp 1.000</li>
                    </ul>
                    <div class="fw-bold">Informasi Pembagian Hasil:</div>
                    <ul class="mb-0">
                        <li>Bagian Admin/Owner: 80% dari total penjualan</li>
                        <li>Bagian Karyawan: 20% dari total penjualan (dikurangi uang yang terpakai)</li>
                    </ul>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.daily-sales.show', $sale->id) }}" class="btn btn-light me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Otomatis hitung hasil terjual saat input berubah
    document.addEventListener('DOMContentLoaded', function() {
        const doughBroughtInput = document.getElementById('dough_brought');
        const doughRemainingPrintedInput = document.getElementById('dough_remaining_printed');
        const doughRemainingUnprintedInput = document.getElementById('dough_remaining_unprinted');
        const employeeExpensesInput = document.getElementById('employee_expenses');
        const totalSalesInput = document.getElementById('total_sales');

        // Fungsi untuk mengkalkulasi dan menampilkan estimasi
        function calculateEstimate() {
            const doughBrought = parseFloat(doughBroughtInput.value) || 0;
            const doughRemainingUnprinted = parseInt(doughRemainingUnprintedInput.value) || 0;
            const doughRemainingPrinted = parseInt(doughRemainingPrintedInput.value) || 0;
            
            if (doughBrought > 0) {
                // 1 KG adonan = 198 pcs, 1 kue = Rp 1.000
                const totalPieces = doughBrought * 198;
                const soldPieces = totalPieces - doughRemainingUnprinted - doughRemainingPrinted;
                const estimatedSales = soldPieces * 1000;
                
                // Update placeholder total penjualan
                totalSalesInput.placeholder = `Estimasi: Rp ${Math.max(0, estimatedSales).toLocaleString('id-ID')}`;
                
                // Hitung bagian karyawan (20% dikurangi expenses)
                const employeeExpenses = parseInt(employeeExpensesInput.value) || 0;
                const employeeShare = Math.max(0, (estimatedSales * 0.2) - employeeExpenses);
                
                // Tampilkan estimasi bagian karyawan
                employeeExpensesInput.placeholder = `Bagian karyawan: Rp ${employeeShare.toLocaleString('id-ID')}`;
            }
        }

        // Panggil fungsi saat DOM siap dan saat input berubah
        calculateEstimate();
        doughBroughtInput.addEventListener('input', calculateEstimate);
        doughRemainingPrintedInput.addEventListener('input', calculateEstimate);
        doughRemainingUnprintedInput.addEventListener('input', calculateEstimate);
        employeeExpensesInput.addEventListener('input', calculateEstimate);
    });
</script>
@endsection

@endsection 