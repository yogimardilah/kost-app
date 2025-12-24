@extends('layouts.app')

@section('title', 'Edit Pembelian/Ops')

@section('content_header')
    <h1>Edit Pembelian/Ops</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('purchases.update', $purchase) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="kost_id">Kost <span class="text-danger">*</span></label>
                    <select name="kost_id" id="kost_id" class="form-control @error('kost_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kost --</option>
                        @foreach($kosts as $kost)
                            <option value="{{ $kost->id }}" {{ old('kost_id', $purchase->kost_id) == $kost->id ? 'selected' : '' }}>
                                {{ $kost->nama_kost }}
                            </option>
                        @endforeach
                    </select>
                    @error('kost_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi <span class="text-danger">*</span></label>
                    <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                           value="{{ old('description', $purchase->description) }}" required>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="category">Kategori <span class="text-danger">*</span></label>
                    <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="perawatan" {{ old('category', $purchase->category) === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="perbaikan" {{ old('category', $purchase->category) === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="pembelian_barang" {{ old('category', $purchase->category) === 'pembelian_barang' ? 'selected' : '' }}>Pembelian Barang</option>
                        <option value="lainnya" {{ old('category', $purchase->category) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                           value="{{ old('amount', $purchase->amount) }}" step="0.01" required>
                    @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="purchase_date">Tanggal Pembelian <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" 
                           value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                    @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $purchase->notes) }}</textarea>
                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="file">Upload File (Bukti, Invoice, dll)</label>
                    
                    @if($purchase->file_path)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-file"></i> File saat ini: 
                            <a href="{{ asset('storage/' . $purchase->file_path) }}" target="_blank" class="font-weight-bold">
                                {{ basename($purchase->file_path) }}
                            </a>
                            <br>
                            <small>Pilih file baru jika ingin mengganti</small>
                        </div>
                    @endif

                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="file" name="file"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xlsx,.xls">
                            <label class="custom-file-label" for="file">Pilih file</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Format: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX. Maks: 5MB. File akan dikompres otomatis jika dipilih.
                    </small>
                    <div id="fileInfo" class="mt-3" style="display:none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div id="filePreview"></div>
                                <p class="text-info mb-0 mt-2">
                                    <i class="fas fa-file"></i> <span id="fileName"></span>
                                    <br>
                                    <small>Ukuran: <span id="fileSize"></span></small>
                                </p>
                            </div>
                        </div>
                    </div>
                    @error('file') <span class="text-danger"><small>{{ $message }}</small></span> @enderror
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/compressorjs@1.10.1/dist/compressor.min.js"></script>
    <script>
        const fileInput = document.getElementById('file');
        const fileLabel = document.querySelector('.custom-file-label');
        const fileInfo = document.getElementById('fileInfo');
        const filePreview = document.getElementById('filePreview');
        const fileNameSpan = document.getElementById('fileName');
        const fileSizeSpan = document.getElementById('fileSize');

        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) return;

            // Update label
            fileLabel.textContent = file.name;

            // For image files, compress them
            if (file.type.startsWith('image/')) {
                new Compressor(file, {
                    quality: 0.6,
                    maxWidth: 1920,
                    maxHeight: 1920,
                    convertSize: Infinity,
                    success(result) {
                        // Create a new File object from the compressed blob
                        const compressedFile = new File([result], file.name, { type: result.type });
                        
                        // Replace the file in input
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        fileInput.files = dataTransfer.files;

                        // Display file info and preview
                        fileNameSpan.textContent = compressedFile.name;
                        fileSizeSpan.textContent = formatFileSize(compressedFile.size);
                        displayPreview(compressedFile);
                        fileInfo.style.display = 'block';

                        console.log('File berhasil dikompres. Ukuran: ' + formatFileSize(compressedFile.size));
                    },
                    error(err) {
                        // If compression fails, use original file
                        fileNameSpan.textContent = file.name;
                        fileSizeSpan.textContent = formatFileSize(file.size);
                        displayPreview(file);
                        fileInfo.style.display = 'block';
                        console.error('Kompresi gagal, menggunakan file original:', err.message);
                    }
                });
            } else {
                // For non-image files, just display info
                fileNameSpan.textContent = file.name;
                fileSizeSpan.textContent = formatFileSize(file.size);
                displayPreview(file);
                fileInfo.style.display = 'block';
            }
        });

        function displayPreview(file) {
            filePreview.innerHTML = '';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '300px';
                    img.style.borderRadius = '4px';
                    filePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                const icon = document.createElement('div');
                icon.innerHTML = '<i class="fas fa-file-pdf" style="font-size: 48px; color: #d32f2f;"></i>';
                icon.style.textAlign = 'center';
                icon.style.padding = '20px';
                filePreview.appendChild(icon);
            } else if (file.type.includes('word') || file.type.includes('document')) {
                const icon = document.createElement('div');
                icon.innerHTML = '<i class="fas fa-file-word" style="font-size: 48px; color: #2196F3;"></i>';
                icon.style.textAlign = 'center';
                icon.style.padding = '20px';
                filePreview.appendChild(icon);
            } else if (file.type.includes('sheet') || file.type.includes('excel')) {
                const icon = document.createElement('div');
                icon.innerHTML = '<i class="fas fa-file-excel" style="font-size: 48px; color: #4CAF50;"></i>';
                icon.style.textAlign = 'center';
                icon.style.padding = '20px';
                filePreview.appendChild(icon);
            } else {
                const icon = document.createElement('div');
                icon.innerHTML = '<i class="fas fa-file" style="font-size: 48px; color: #666;"></i>';
                icon.style.textAlign = 'center';
                icon.style.padding = '20px';
                filePreview.appendChild(icon);
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
@endsection
