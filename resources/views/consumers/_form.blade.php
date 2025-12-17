@php
    $consumer = $consumer ?? null;
    $action = $consumer ? route('consumers.update', $consumer) : route('consumers.store');
@endphp

<form action="{{ $action }}" method="POST">
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

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $consumer ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('consumers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
