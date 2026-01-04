@php
    $consumer = $consumer ?? null;
    $action = $consumer ? route('consumers.update', $consumer) : route('consumers.store');
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($consumer)
        @method('PUT')
    @endif

    <div class="form-group mb-3">
        <label for="nik">NIK <span class="text-danger">*</span></label>
        <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $consumer->nik ?? '') }}" required>
        @error('nik') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="nama">Nama <span class="text-danger">*</span></label>
        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $consumer->nama ?? '') }}" required>
        @error('nama') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="no_hp">No. HP <span class="text-danger">*</span></label>
        <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $consumer->no_hp ?? '') }}" required>
        @error('no_hp') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="kendaraan">Kendaraan</label>
        <input type="text" name="kendaraan" id="kendaraan" class="form-control @error('kendaraan') is-invalid @enderror" value="{{ old('kendaraan', $consumer->kendaraan ?? '') }}">
        @error('kendaraan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="tanda_pengenal">Tanda Pengenal (KTP/SIM/Passport)</label>
        <input type="file" name="tanda_pengenal" id="tanda_pengenal" class="form-control @error('tanda_pengenal') is-invalid @enderror" accept="image/*,.pdf" onchange="previewFile(this)">
        @error('tanda_pengenal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        
        @if($consumer && $consumer->tanda_pengenal)
            <div class="mt-2" id="currentFilePreview">
                <label class="d-block"><strong>File Saat Ini:</strong></label>
                @php
                    $extension = pathinfo($consumer->tanda_pengenal, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                @endphp
                
                @if($isImage)
                    <div class="border rounded p-2" style="max-width: 400px;">
                        <img src="{{ Storage::url($consumer->tanda_pengenal) }}" alt="Tanda Pengenal" class="img-fluid rounded" style="max-height: 300px;">
                        <div class="mt-2">
                            <a href="{{ Storage::url($consumer->tanda_pengenal) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-external-link-alt"></i> Lihat Full Size
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-file-pdf"></i> 
                        <a href="{{ Storage::url($consumer->tanda_pengenal) }}" target="_blank" class="alert-link">
                            Lihat Dokumen PDF
                        </a>
                    </div>
                @endif
            </div>
        @endif
        
        <div id="newFilePreview" class="mt-2" style="display: none;">
            <label class="d-block"><strong>Preview File Baru:</strong></label>
            <div class="border rounded p-2" style="max-width: 400px;">
                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px; display: none;">
                <div id="previewPdf" class="alert alert-info" style="display: none;">
                    <i class="fas fa-file-pdf"></i> <span id="pdfFileName"></span>
                </div>
            </div>
        </div>
        
        <small class="form-text text-muted">
            Format: JPG, PNG, GIF, WebP, PDF (Max: 2MB)<br>
            <i class="fas fa-info-circle"></i> Gambar akan dikompres otomatis
        </small>
    </div>

    <script>
        function previewFile(input) {
            const preview = document.getElementById('newFilePreview');
            const previewImage = document.getElementById('previewImage');
            const previewPdf = document.getElementById('previewPdf');
            const pdfFileName = document.getElementById('pdfFileName');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                preview.style.display = 'block';
                
                if (file.type.startsWith('image/')) {
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewPdf.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    previewImage.style.display = 'none';
                    previewPdf.style.display = 'block';
                    pdfFileName.textContent = file.name;
                }
            } else {
                preview.style.display = 'none';
            }
        }
    </script>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $consumer ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('consumers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
