@php
    $consumer = $consumer ?? null;
    $action = $consumer ? route('consumers.update', $consumer) : route('consumers.store');
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="consumerForm">
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

    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-phone-square"></i> Kontak Darurat</h5>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="kontak_darurat_nama">Nama Kontak Darurat</label>
                <input type="text" name="kontak_darurat_nama" id="kontak_darurat_nama" class="form-control @error('kontak_darurat_nama') is-invalid @enderror" value="{{ old('kontak_darurat_nama', $consumer->kontak_darurat_nama ?? '') }}" placeholder="Contoh: Budi Santoso">
                @error('kontak_darurat_nama') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-3">
                <label for="kontak_darurat_hubungan">Hubungan</label>
                <select name="kontak_darurat_hubungan" id="kontak_darurat_hubungan" class="form-control @error('kontak_darurat_hubungan') is-invalid @enderror">
                    <option value="">-- Pilih Hubungan --</option>
                    <option value="Orang Tua" {{ old('kontak_darurat_hubungan', $consumer->kontak_darurat_hubungan ?? '') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                    <option value="Saudara" {{ old('kontak_darurat_hubungan', $consumer->kontak_darurat_hubungan ?? '') == 'Saudara' ? 'selected' : '' }}>Saudara</option>
                    <option value="Suami/Istri" {{ old('kontak_darurat_hubungan', $consumer->kontak_darurat_hubungan ?? '') == 'Suami/Istri' ? 'selected' : '' }}>Suami/Istri</option>
                    <option value="Teman" {{ old('kontak_darurat_hubungan', $consumer->kontak_darurat_hubungan ?? '') == 'Teman' ? 'selected' : '' }}>Teman</option>
                    <option value="Lainnya" {{ old('kontak_darurat_hubungan', $consumer->kontak_darurat_hubungan ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('kontak_darurat_hubungan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="form-group mb-0">
                <label for="kontak_darurat_no_hp">No. HP Kontak Darurat</label>
                <input type="text" name="kontak_darurat_no_hp" id="kontak_darurat_no_hp" class="form-control @error('kontak_darurat_no_hp') is-invalid @enderror" value="{{ old('kontak_darurat_no_hp', $consumer->kontak_darurat_no_hp ?? '') }}" placeholder="Contoh: 08123456789">
                @error('kontak_darurat_no_hp') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/compressorjs@1.2.1/dist/compressor.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('tanda_pengenal');
            if (!fileInput) {
                return;
            }

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (!file) {
                    return;
                }

                // Compress only image files; keep PDF untouched.
                if (!(file.type && file.type.startsWith('image/'))) {
                    return;
                }

                new Compressor(file, {
                    quality: 0.65,
                    maxWidth: 1600,
                    maxHeight: 1600,
                    convertSize: 500000,
                    success(result) {
                        const compressedFile = new File([result], file.name, { type: result.type, lastModified: Date.now() });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        fileInput.files = dataTransfer.files;

                        // Refresh preview with compressed content.
                        previewFile(fileInput);
                    },
                    error(err) {
                        console.error('Compression failed:', err);
                    }
                });
            });
        });
    </script>

    <div class="form-group">
        <button type="submit" class="btn btn-primary" id="submitConsumerBtn">
            <span class="submit-label-default">
                <i class="fas fa-save"></i> {{ $consumer ? 'Update' : 'Simpan' }}
            </span>
            <span class="submit-label-loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Menyimpan...
            </span>
        </button>
        <a href="{{ route('consumers.index') }}" class="btn btn-secondary">Batal</a>

        <div id="consumerSubmitLoading" class="mt-3" style="display: none; max-width: 420px;">
            <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
            </div>
            <small class="text-muted d-block mt-1">Menyimpan data penyewa, mohon tunggu...</small>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('consumerForm');
        const submitBtn = document.getElementById('submitConsumerBtn');
        const submitLoading = document.getElementById('consumerSubmitLoading');

        if (!form || !submitBtn) {
            return;
        }

        let isSubmitting = false;

        form.addEventListener('submit', function(event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }

            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');

            const defaultLabel = submitBtn.querySelector('.submit-label-default');
            const loadingLabel = submitBtn.querySelector('.submit-label-loading');

            if (defaultLabel && loadingLabel) {
                defaultLabel.style.display = 'none';
                loadingLabel.style.display = 'inline';
            }

            if (submitLoading) {
                submitLoading.style.display = 'block';
            }
        });
    });
</script>
