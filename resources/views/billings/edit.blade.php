@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content_header')
<h1><i class="fas fa-edit"></i> Edit Tagihan</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice"></i> Informasi Tagihan</h3>
                <div class="card-tools">
                    <a href="{{ route('billings.show', $billing) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('billings.update', $billing) }}" method="POST" id="billingForm">
                    @csrf
                    @method('PUT')

                    <!-- Info Section -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Invoice</span>
                                    <span class="info-box-number">{{ $billing->invoice_number }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Penyewa</span>
                                    <span class="info-box-number" style="font-size: 16px;">{{ $billing->consumer->nama ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-door-open"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kamar</span>
                                    <span class="info-box-number">{{ $billing->room->nomor_kamar ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <input type="hidden" name="status" value="{{ old('status', $billing->status) }}">
                                <select class="form-control" disabled style="pointer-events: none; background-color: #e9ecef;">
                                    <option value="pending" {{ old('status', $billing->status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="sebagian" {{ old('status', $billing->status) == 'sebagian' ? 'selected' : '' }}>💰 Sebagian</option>
                                    <option value="lunas" {{ old('status', $billing->status) == 'lunas' ? 'selected' : '' }}>✅ Lunas</option>
                                </select>
                                <small class="form-text text-muted">Status otomatis berdasarkan pembayaran</small>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Periode Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Periode Awal <span class="text-danger">*</span></label>
                                <input type="date" name="periode_awal" class="form-control @error('periode_awal') is-invalid @enderror" 
                                       value="{{ old('periode_awal', $billing->periode_awal->format('Y-m-d')) }}" required>
                                @error('periode_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-check"></i> Periode Akhir <span class="text-danger">*</span></label>
                                <input type="date" name="periode_akhir" class="form-control @error('periode_akhir') is-invalid @enderror" 
                                       value="{{ old('periode_akhir', $billing->periode_akhir->format('Y-m-d')) }}" required>
                                @error('periode_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Detail Tagihan Section -->
                    <h5 class="mb-3"><i class="fas fa-list-ul text-primary"></i> Detail Tagihan</h5>
                    
                    <div id="detailsContainer" class="mb-3">
                        @forelse($billing->details as $index => $detail)
                        <div class="detail-row card mb-2">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-tag"></i> Keterangan <span class="text-danger">*</span></label>
                                            <input type="text" name="details[{{ $index }}][keterangan]" 
                                                   class="form-control" 
                                                   placeholder="Contoh: Sewa Kamar Bulan Januari"
                                                   value="{{ old('details.'.$index.'.keterangan', $detail->keterangan) }}" required>
                                            <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                            <input type="hidden" name="details[{{ $index }}][harga]" 
                                                   class="detail-harga" 
                                                   value="{{ old('details.'.$index.'.harga', $detail->harga) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-calculator"></i> Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="details[{{ $index }}][qty]" 
                                                   class="form-control detail-qty text-center" 
                                                   value="{{ old('details.'.$index.'.qty', $detail->qty) }}" 
                                                   min="1" step="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-money-bill-wave"></i> Subtotal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="details[{{ $index }}][subtotal]" 
                                                       class="form-control detail-subtotal" 
                                                       value="{{ old('details.'.$index.'.subtotal', $detail->subtotal) }}" 
                                                       min="0" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="display:none;">
                                        <label class="mb-1">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block btn-remove-detail" title="Hapus Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada detail tagihan. Klik tombol <strong>"Tambah Detail"</strong> untuk menambahkan item.
                        </div>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-success btn-sm mb-3" id="btnAddDetail" style="display: none;">
                        <i class="fas fa-plus-circle"></i> Tambah Detail
                    </button>

                    <!-- Summary Detail -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-calculator"></i> Total dari Detail</label>
                                        <h4 class="text-info mb-0" id="autoTotal">Rp 0</h4>
                                        <small class="text-muted">Dihitung otomatis dari subtotal detail</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-file-invoice-dollar"></i> Total Tagihan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" name="total_tagihan" id="total_tagihan" 
                                                   class="form-control form-control-lg font-weight-bold @error('total_tagihan') is-invalid @enderror" 
                                                   value="{{ old('total_tagihan', $billing->total_tagihan) }}" 
                                                   readonly style="background-color: #e9ecef;"
                                                   min="0" step="0.01" required>
                                        </div>
                                        <small class="text-muted">Dihitung otomatis dari total detail</small>
                                        @error('total_tagihan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Pembayaran Section -->
                    <h5 class="mb-3"><i class="fas fa-money-bill-wave text-success"></i> Pembayaran</h5>
                    
                    <div id="paymentsContainer" class="mb-3">
                        @forelse($billing->payments as $index => $payment)
                        <div class="payment-row card mb-2">
                            <div class="card-body p-3 bg-light">
                                <input type="hidden" name="payments[{{ $index }}][id]" value="{{ $payment->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-calendar"></i> Tanggal Bayar <span class="text-danger">*</span></label>
                                            <input type="date" name="payments[{{ $index }}][tanggal_bayar]" 
                                                   class="form-control" 
                                                   value="{{ old('payments.'.$index.'.tanggal_bayar', $payment->tanggal_bayar->format('Y-m-d')) }}" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-dollar-sign"></i> Jumlah Bayar <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="payments[{{ $index }}][jumlah]" 
                                                       class="form-control payment-jumlah" 
                                                       value="{{ old('payments.'.$index.'.jumlah', $payment->jumlah) }}" 
                                                       min="0" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label class="mb-1"><i class="fas fa-credit-card"></i> Metode Pembayaran <span class="text-danger">*</span></label>
                                            <select name="payments[{{ $index }}][metode]" class="form-control" required>
                                                <option value="tunai" {{ old('payments.'.$index.'.metode', $payment->metode) == 'tunai' ? 'selected' : '' }}>💵 Tunai</option>
                                                <option value="transfer" {{ old('payments.'.$index.'.metode', $payment->metode) == 'transfer' ? 'selected' : '' }}>🏦 Transfer</option>
                                                <option value="qris" {{ old('payments.'.$index.'.metode', $payment->metode) == 'qris' ? 'selected' : '' }}>📱 QRIS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="display:none;">
                                        <label class="mb-1">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block btn-remove-payment" title="Hapus Pembayaran">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada pembayaran. Klik tombol <strong>"Tambah Pembayaran"</strong> untuk mencatat pembayaran.
                        </div>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-success btn-sm mb-3" id="btnAddPayment" style="display: none;">
                        <i class="fas fa-plus-circle"></i> Tambah Pembayaran
                    </button>

                    <!-- Summary Pembayaran -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-check-circle"></i> Total Dibayar</label>
                                        <h4 class="text-success mb-0" id="totalDibayar">Rp 0</h4>
                                        <small class="text-muted">Dihitung otomatis dari pembayaran</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-exclamation-circle"></i> Sisa Tagihan</label>
                                        <h4 class="text-danger mb-0" id="sisaTagihan">Rp 0</h4>
                                        <small class="text-muted">Total Tagihan - Total Dibayar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Warning & Actions -->
                    <div class="alert alert-warning">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Perhatian!</h5>
                        <ul class="mb-0">
                            <li>Status akan <strong>otomatis disesuaikan</strong> berdasarkan Total Dibayar vs Total Tagihan</li>
                            <li>Pastikan semua data sudah <strong>sesuai</strong> sebelum menyimpan</li>
                        </ul>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('billings.show', $billing) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize counters for dynamic rows
let detailIndex = {{ $billing->details->count() }};
let paymentIndex = {{ $billing->payments->count() }};

// Format number to Rupiah
function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

// Calculate subtotal for a detail row
function calculateSubtotal(row) {
    const qty = parseFloat(row.querySelector('.detail-qty').value) || 0;
    const harga = parseFloat(row.querySelector('.detail-harga').value) || 0;
    const subtotal = qty * harga;
    row.querySelector('.detail-subtotal').value = subtotal;
    calculateTotal();
}

// Calculate total tagihan from all details
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.detail-subtotal').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('autoTotal').textContent = formatRupiah(total);
    document.getElementById('total_tagihan').value = total;
    calculateSisa();
}

// Calculate total dibayar from all payments
function calculatePayments() {
    let totalDibayar = 0;
    document.querySelectorAll('.payment-jumlah').forEach(input => {
        totalDibayar += parseFloat(input.value) || 0;
    });
    document.getElementById('totalDibayar').textContent = formatRupiah(totalDibayar);
    calculateSisa();
}

// Calculate sisa tagihan
function calculateSisa() {
    const totalTagihan = parseFloat(document.getElementById('total_tagihan').value) || 0;
    let totalDibayar = 0;
    document.querySelectorAll('.payment-jumlah').forEach(input => {
        totalDibayar += parseFloat(input.value) || 0;
    });
    const sisa = totalTagihan - totalDibayar;
    document.getElementById('sisaTagihan').textContent = formatRupiah(sisa);
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Attach listeners to existing detail rows
    document.querySelectorAll('.detail-row').forEach(row => {
        const qtyInput = row.querySelector('.detail-qty');
        const hargaInput = row.querySelector('.detail-harga');
        const subtotalInput = row.querySelector('.detail-subtotal');
        
        if (qtyInput) qtyInput.addEventListener('input', () => calculateSubtotal(row));
        if (hargaInput) hargaInput.addEventListener('input', () => calculateSubtotal(row));
        if (subtotalInput) {
            subtotalInput.addEventListener('input', calculateTotal);
            subtotalInput.addEventListener('change', calculateTotal);
        }
    });
    
    // Attach listeners to existing payments
    document.querySelectorAll('.payment-jumlah').forEach(input => {
        input.addEventListener('input', calculatePayments);
        input.addEventListener('change', calculatePayments);
    });
    
    // Calculate initial totals
    calculateTotal();
    calculatePayments();
    
    // Button: Add Detail (hidden but functional)
    document.getElementById('btnAddDetail').addEventListener('click', function() {
        const container = document.getElementById('detailsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'detail-row card mb-2';
        newRow.innerHTML = `
            <div class="card-body p-3 bg-light">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-info-circle"></i> Keterangan <span class="text-danger">*</span></label>
                            <input type="text" name="details[\${detailIndex}][keterangan]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-sort-numeric-up"></i> Qty</label>
                            <input type="number" name="details[\${detailIndex}][qty]" class="form-control detail-qty" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-tag"></i> Harga</label>
                            <input type="number" name="details[\${detailIndex}][harga]" class="form-control detail-harga" value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-dollar-sign"></i> Subtotal <span class="text-danger">*</span></label>
                            <input type="number" name="details[\${detailIndex}][subtotal]" class="form-control detail-subtotal" value="0" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-1" style="display:none;">
                        <label class="mb-1">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block btn-remove-detail" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        detailIndex++;
        
        // Attach listeners to new row
        const qtyInput = newRow.querySelector('.detail-qty');
        const hargaInput = newRow.querySelector('.detail-harga');
        const subtotalInput = newRow.querySelector('.detail-subtotal');
        
        if (qtyInput) qtyInput.addEventListener('input', () => calculateSubtotal(newRow));
        if (hargaInput) hargaInput.addEventListener('input', () => calculateSubtotal(newRow));
        if (subtotalInput) {
            subtotalInput.addEventListener('input', calculateTotal);
            subtotalInput.addEventListener('change', calculateTotal);
        }
        
        calculateTotal();
    });
    
    // Event delegation for remove detail button
    document.getElementById('detailsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-detail')) {
            if (confirm('Apakah Anda yakin ingin menghapus detail ini?')) {
                e.target.closest('.detail-row').remove();
                calculateTotal();
            }
        }
    });
    
    // Button: Add Payment (hidden but functional)
    document.getElementById('btnAddPayment').addEventListener('click', function() {
        const container = document.getElementById('paymentsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'payment-row card mb-2';
        newRow.innerHTML = `
            <div class="card-body p-3 bg-light">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-calendar"></i> Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="payments[\${paymentIndex}][tanggal_bayar]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-dollar-sign"></i> Jumlah Bayar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="payments[\${paymentIndex}][jumlah]" class="form-control payment-jumlah" value="0" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-1"><i class="fas fa-credit-card"></i> Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payments[\${paymentIndex}][metode]" class="form-control" required>
                                <option value="tunai">💵 Tunai</option>
                                <option value="transfer">🏦 Transfer</option>
                                <option value="qris">📱 QRIS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1" style="display:none;">
                        <label class="mb-1">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block btn-remove-payment" title="Hapus Pembayaran">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        paymentIndex++;
        
        // Attach listener to new payment
        const jumlahInput = newRow.querySelector('.payment-jumlah');
        jumlahInput.addEventListener('input', calculatePayments);
        jumlahInput.addEventListener('change', calculatePayments);
        
        calculatePayments();
    });
    
    // Event delegation for remove payment button
    document.getElementById('paymentsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-payment')) {
            if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')) {
                e.target.closest('.payment-row').remove();
                calculatePayments();
            }
        }
    });
    
});
</script>

@endsection
