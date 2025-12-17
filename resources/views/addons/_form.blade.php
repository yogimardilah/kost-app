@php
    $addon = $addon ?? null;
    $action = $addon ? route('addons.update', $addon) : route('addons.store');
    $method = $addon ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($addon)
        @method('PUT')
    @endif

    <div class="form-group mb-3">
        <label for="nama_addon">Nama Addon <span class="text-danger">*</span></label>
        <input type="text" name="nama_addon" id="nama_addon" class="form-control @error('nama_addon') is-invalid @enderror" 
               value="{{ old('nama_addon', $addon->nama_addon ?? '') }}" placeholder="Contoh: WiFi, TV Kabel, AC, dll" required>
        @error('nama_addon') 
            <div class="invalid-feedback d-block">{{ $message }}</div> 
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="harga">Harga (Rp) <span class="text-danger">*</span></label>
        <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" 
               value="{{ old('harga', $addon->harga ?? '') }}" min="0" step="0.01" required>
        @error('harga') 
            <div class="invalid-feedback d-block">{{ $message }}</div> 
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="satuan">Satuan <span class="text-danger">*</span></label>
        <input type="text" name="satuan" id="satuan" class="form-control @error('satuan') is-invalid @enderror" 
               value="{{ old('satuan', $addon->satuan ?? '') }}" placeholder="Contoh: bulan, tahun, per kamar, dll" required>
        @error('satuan') 
            <div class="invalid-feedback d-block">{{ $message }}</div> 
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $addon ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('addons.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
    </div>
</form>
