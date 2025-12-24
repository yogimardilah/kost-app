@php
    $types = ['single' => 'Single', 'double' => 'Double', 'suite' => 'Suite'];
    $statuses = ['tersedia' => 'Tersedia', 'terisi' => 'Terisi'];
    $room = $room ?? null;
    $action = $room ? route('rooms.update', $room) : route('rooms.store');
    $method = $room ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($room)
        @method('PUT')
    @endif

    <div class="form-group mb-3">
        <label for="nomor_kamar">Nomor Kamar <span class="text-danger">*</span></label>
        <input type="text" name="nomor_kamar" id="nomor_kamar" class="form-control @error('nomor_kamar') is-invalid @enderror" value="{{ old('nomor_kamar', $room->nomor_kamar ?? '') }}" required>
        @error('nomor_kamar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="jenis_kamar">Jenis Kamar <span class="text-danger">*</span></label>
        <select name="jenis_kamar" id="jenis_kamar" class="form-control @error('jenis_kamar') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis --</option>
            @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ old('jenis_kamar', $room->jenis_kamar ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenis_kamar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="harga">Harga Bulanan (Rp) <span class="text-danger">*</span></label>
        <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $room->harga ?? '') }}" min="50000" required>
        <small class="form-text text-muted">Minimal Rp 50.000 - Harga untuk periode 30 hari</small>
        @error('harga') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="harga_harian">Harga Harian (Rp)</label>
        <input type="number" name="harga_harian" id="harga_harian" class="form-control @error('harga_harian') is-invalid @enderror" value="{{ old('harga_harian', $room->harga_harian ?? '') }}" min="5000">
        <small class="form-text text-muted">Opsional - Harga untuk sewa per hari</small>
        @error('harga_harian') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="fasilitas">Fasilitas Kamar</label>
        <textarea name="fasilitas" id="fasilitas" class="form-control @error('fasilitas') is-invalid @enderror" rows="4" placeholder="Contoh: AC, TV, Kamar mandi dalam, WiFi, Lemari, Kasur, Meja belajar, dll.">{{ old('fasilitas', $room->fasilitas ?? '') }}</textarea>
        <small class="form-text text-muted">Opsional - Deskripsi fasilitas yang tersedia di kamar ini</small>
        @error('fasilitas') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    @if($room)
    <div class="form-group mb-3">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ old('status', $room->status ?? 'tersedia') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>
    @endif

    <div class="form-group mb-3">
        <label for="kost_id">Kost <span class="text-danger">*</span></label>
        <select name="kost_id" id="kost_id" class="form-control @error('kost_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kost --</option>
            @foreach($kosts as $kost)
                <option value="{{ $kost->id }}" {{ old('kost_id', $room->kost_id ?? request('kost_id') ?? '') == $kost->id ? 'selected' : '' }}>
                    {{ $kost->nama_kost }}
                </option>
            @endforeach
        </select>
        @error('kost_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary">{{ $room ? 'Update' : 'Simpan' }}</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
