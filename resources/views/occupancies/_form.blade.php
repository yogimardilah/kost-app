@php
    $occupancy = $occupancy ?? null;
    $action = $occupancy ? route('occupancies.update', $occupancy) : route('occupancies.store');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($occupancy)
        @method('PUT')
    @endif

    <div class="form-group mb-3">
        <label for="room_id">Kamar <span class="text-danger">*</span></label>
        <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kamar --</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" {{ old('room_id', $occupancy->room_id ?? '') == $room->id ? 'selected' : '' }}>
                    {{ $room->nomor_kamar }} - {{ $room->jenis_kamar }}
                </option>
            @endforeach
        </select>
        @error('room_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="consumer_id">Penyewa <span class="text-danger">*</span></label>
        <select name="consumer_id" id="consumer_id" class="form-control @error('consumer_id') is-invalid @enderror" required>
            <option value="">-- Pilih Penyewa --</option>
            @foreach($consumers as $c)
                <option value="{{ $c->id }}" {{ old('consumer_id', $occupancy->consumer_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->nama }} ({{ $c->nik }})
                </option>
            @endforeach
        </select>
        @error('consumer_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', $occupancy->tanggal_masuk ?? '') }}" required>
        @error('tanggal_masuk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="tanggal_keluar">Tanggal Keluar</label>
        <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror" value="{{ old('tanggal_keluar', $occupancy->tanggal_keluar ?? '') }}">
        @error('tanggal_keluar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="aktif" {{ old('status', $occupancy->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="selesai" {{ old('status', $occupancy->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="batal" {{ old('status', $occupancy->status ?? '') == 'batal' ? 'selected' : '' }}>Batal</option>
        </select>
        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $occupancy ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('occupancies.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
