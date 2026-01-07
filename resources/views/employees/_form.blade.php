<div class="card-body">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- NIK -->
            <div class="form-group">
                <label for="nik">NIK <span class="text-danger">*</span></label>
                <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" 
                       value="{{ old('nik', $employee->nik ?? '') }}" required>
                @error('nik')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" 
                       value="{{ old('nama', $employee->nama ?? '') }}" required>
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jabatan -->
            <div class="form-group">
                <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" 
                       value="{{ old('jabatan', $employee->jabatan ?? '') }}" required 
                       list="jabatan-list" placeholder="Contoh: Manager, Staff Kebersihan, Security">
                <datalist id="jabatan-list">
                    <option value="Manager">
                    <option value="Supervisor">
                    <option value="Staff Administrasi">
                    <option value="Staff Kebersihan">
                    <option value="Security">
                    <option value="Teknisi">
                    <option value="Customer Service">
                </datalist>
                @error('jabatan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- No. HP -->
            <div class="form-group">
                <label for="no_hp">No. HP <span class="text-danger">*</span></label>
                <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" 
                       value="{{ old('no_hp', $employee->no_hp ?? '') }}" required>
                @error('no_hp')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Alamat -->
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $employee->alamat ?? '') }}</textarea>
                @error('alamat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Tanggal Bergabung -->
            <div class="form-group">
                <label for="tanggal_bergabung">Tanggal Bergabung <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_bergabung" id="tanggal_bergabung" 
                       class="form-control @error('tanggal_bergabung') is-invalid @enderror" 
                       value="{{ old('tanggal_bergabung', isset($employee) ? $employee->tanggal_bergabung->format('Y-m-d') : '') }}" required>
                @error('tanggal_bergabung')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Berakhir -->
            <div class="form-group">
                <label for="tanggal_berakhir">Tanggal Berakhir (Opsional)</label>
                <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" 
                       class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                       value="{{ old('tanggal_berakhir', isset($employee) && $employee->tanggal_berakhir ? $employee->tanggal_berakhir->format('Y-m-d') : '') }}">
                <small class="form-text text-muted">Kosongkan jika karyawan masih aktif</small>
                @error('tanggal_berakhir')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gaji -->
            <div class="form-group">
                <label for="gaji">Gaji <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" name="gaji" id="gaji" class="form-control @error('gaji') is-invalid @enderror" 
                           value="{{ old('gaji', $employee->gaji ?? '') }}" required min="0" step="1000">
                </div>
                @error('gaji')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Gajian -->
            <div class="form-group">
                <label for="tanggal_gajian">Tanggal Gajian (1-31) <span class="text-danger">*</span></label>
                <input type="number" name="tanggal_gajian" id="tanggal_gajian" 
                       class="form-control @error('tanggal_gajian') is-invalid @enderror" 
                       value="{{ old('tanggal_gajian', $employee->tanggal_gajian ?? 1) }}" 
                       required min="1" max="31">
                <small class="form-text text-muted">Tanggal pembayaran gaji setiap bulannya</small>
                @error('tanggal_gajian')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="aktif" {{ old('status', $employee->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak aktif" {{ old('status', $employee->status ?? '') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Foto -->
            <div class="form-group">
                <label for="foto">Foto</label>
                <div class="custom-file">
                    <input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept="image/*">
                    <label class="custom-file-label" for="foto">Pilih file...</label>
                </div>
                <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB (akan otomatis dikompres)</small>
                @error('foto')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
                
                <!-- Preview Container -->
                <div id="photo-preview-container" class="mt-2" style="display: none;">
                    <img id="photo-preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    <p class="text-muted mt-1">Preview foto yang akan diupload</p>
                    <button type="button" class="btn btn-sm btn-danger" id="remove-photo">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
                
                @if(isset($employee) && $employee->foto)
                    <div class="mt-2" id="existing-photo">
                        <img src="{{ asset('storage/' . $employee->foto) }}" alt="Foto Karyawan" class="img-thumbnail" style="max-width: 200px;">
                        <p class="text-muted mt-1">Foto saat ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    // Compress and preview image
    const fotoInput = document.getElementById('foto');
    const photoPreview = document.getElementById('photo-preview');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const removePhotoBtn = document.getElementById('remove-photo');
    const existingPhoto = document.getElementById('existing-photo');
    const fileLabel = document.querySelector('.custom-file-label');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (!file) return;

        // Update label
        fileLabel.textContent = file.name;

        // Check if file is image
        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar!');
            resetPhotoInput();
            return;
        }

        // Check file size (max 5MB before compression)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            resetPhotoInput();
            return;
        }

        // Read and display image
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Compress image
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Resize if too large (max 1200px width)
                const maxWidth = 1200;
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob with compression
                canvas.toBlob(function(blob) {
                    // Create new file from blob
                    const compressedFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    // Update file input with compressed file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    fotoInput.files = dataTransfer.files;

                    // Show preview
                    photoPreview.src = canvas.toDataURL('image/jpeg', 0.8);
                    photoPreviewContainer.style.display = 'block';
                    
                    // Hide existing photo if present
                    if (existingPhoto) {
                        existingPhoto.style.display = 'none';
                    }

                    // Show compressed size
                    const compressedSize = (blob.size / 1024).toFixed(2);
                    console.log(`Original: ${(file.size / 1024).toFixed(2)} KB, Compressed: ${compressedSize} KB`);
                }, 'image/jpeg', 0.8);
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Remove photo
    removePhotoBtn.addEventListener('click', function() {
        resetPhotoInput();
    });

    function resetPhotoInput() {
        fotoInput.value = '';
        fileLabel.textContent = 'Pilih file...';
        photoPreviewContainer.style.display = 'none';
        photoPreview.src = '';
        
        // Show existing photo again if present
        if (existingPhoto) {
            existingPhoto.style.display = 'block';
        }
    }
</script>
@endsection
