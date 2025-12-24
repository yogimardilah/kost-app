@extends('layouts.app')

@section('title','Catat Pembayaran')

@section('content_header')
<h1>Catat Pembayaran</h1>
@endsection

@section('content')

@if($billing)
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Detail Tagihan</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Invoice:</strong> {{ $billing->invoice_number }}<br>
                <strong>Penyewa:</strong> {{ $billing->consumer->nama ?? '-' }}<br>
                <strong>Kamar:</strong> {{ $billing->room->nomor_kamar ?? '-' }}
            </div>
            <div class="col-md-6 text-right">
                <strong>Total Tagihan:</strong> <span class="text-primary">Rp {{ number_format($billing->total_tagihan,0,',','.') }}</span><br>
                <strong>Sudah Dibayar:</strong> <span class="text-success">Rp {{ number_format($totalPaid,0,',','.') }}</span><br>
                <strong>Sisa Tagihan:</strong> <span class="text-danger"><strong>Rp {{ number_format($remaining,0,',','.') }}</strong></span>
            </div>
        </div>
        
        @if($billingDetails->count() > 0)
        <h6>Rincian Tagihan:</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Keterangan</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billingDetails as $i => $detail)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $detail->keterangan }}</td>
                        <td>{{ $detail->qty }}</td>
                        <td>Rp {{ number_format($detail->harga,0,',','.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="4" class="text-right">Total Tagihan:</th>
                        <th>Rp {{ number_format($billing->total_tagihan,0,',','.') }}</th>
                    </tr>
                    <tr class="bg-success text-white">
                        <th colspan="4" class="text-right">Sudah Dibayar:</th>
                        <th>Rp {{ number_format($totalPaid,0,',','.') }}</th>
                    </tr>
                    <tr class="bg-danger text-white">
                        <th colspan="4" class="text-right">Sisa Tagihan:</th>
                        <th>Rp {{ number_format($remaining,0,',','.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="billing_id">Invoice Tagihan <span class="text-danger">*</span></label>
                <select name="billing_id" id="billing_id" class="form-control @error('billing_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Invoice --</option>
                    @foreach($billings as $b)
                        <option value="{{ $b->id }}" 
                            {{ old('billing_id') == $b->id || ($billing && $billing->id == $b->id) ? 'selected' : '' }}>
                            {{ $b->invoice_number }} - {{ $b->consumer->nama ?? '-' }} (Rp {{ number_format($b->total_tagihan,0,',','.') }})
                        </option>
                    @endforeach
                </select>
                @error('billing_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_bayar">Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                    value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}" required>
                @error('tanggal_bayar')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Pembayaran <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                    step="0.01" min="0" value="{{ old('jumlah') }}" required>
                @error('jumlah')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="metode">Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="metode" id="metode" class="form-control @error('metode') is-invalid @enderror" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="tunai" {{ old('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ old('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
                @error('metode')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bukti_bayar">Keterangan</label>
                <textarea name="bukti_bayar" id="bukti_bayar" class="form-control" rows="3">{{ old('bukti_bayar') }}</textarea>
            </div>

            <div class="form-group">
                <label for="bukti_bayar_file">Upload Bukti Pembayaran</label>
                <input type="file" name="bukti_bayar_file" id="bukti_bayar_file" class="form-control @error('bukti_bayar_file') is-invalid @enderror" accept="image/*,.pdf">
                <small class="text-muted">JPG/PNG/PDF, maks 2 MB. Gambar akan dikompresi otomatis.</small>
                @error('bukti_bayar_file')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <div id="bukti-preview" class="mt-2 d-none">
                    <div class="border rounded p-2">
                        <div class="d-flex align-items-center">
                            <img id="bukti-preview-img" src="#" alt="Preview" class="img-thumbnail mr-2 d-none" style="max-height:120px; max-width:180px;">
                            <div>
                                <div id="bukti-preview-name" class="font-weight-bold small"></div>
                                <div class="text-muted small">Pratinjau bukti pembayaran</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                <a href="{{ route('billings.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/compressorjs@1.2.1/dist/compressor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('bukti_bayar_file');
    const preview = document.getElementById('bukti-preview');
    const previewImg = document.getElementById('bukti-preview-img');
    const previewName = document.getElementById('bukti-preview-name');

    if (!fileInput) return;

    const resetPreview = () => {
        preview.classList.add('d-none');
        previewImg.classList.add('d-none');
        previewImg.src = '#';
        previewName.textContent = '';
    };

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) {
            resetPreview();
            return;
        }

        const isImage = file.type && file.type.startsWith('image/');
        previewName.textContent = `${file.name} (${Math.round(file.size / 1024)} KB)`;

        if (isImage) {
            new Compressor(file, {
                quality: 0.6,
                maxWidth: 1920,
                maxHeight: 1920,
                convertSize: 500000,
                success(result) {
                    // Replace the file input with compressed file while preserving name
                    const compressedFile = new File([result], file.name, { type: result.type, lastModified: Date.now() });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    fileInput.files = dataTransfer.files;

                    const url = URL.createObjectURL(result);
                    previewImg.src = url;
                    previewImg.classList.remove('d-none');
                    preview.classList.remove('d-none');
                },
                error(err) {
                    console.error(err);
                    previewImg.classList.add('d-none');
                    preview.classList.remove('d-none');
                }
            });
        } else {
            previewImg.classList.add('d-none');
            preview.classList.remove('d-none');
        }
    });
});
</script>
@endpush
