@php
    $occupancy = $occupancy ?? null;
    $selectedRoomId = $selectedRoomId ?? null;
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
                <option value="{{ $room->id }}" 
                    {{ old('room_id', $occupancy->room_id ?? $selectedRoomId ?? '') == $room->id ? 'selected' : '' }}>
                    {{ $room->nomor_kamar }} - {{ $room->jenis_kamar }}
                </option>
            @endforeach
        </select>
        @error('room_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="tipe_sewa">Tipe Sewa <span class="text-danger">*</span></label>
        <select name="tipe_sewa" id="tipe_sewa" class="form-control @error('tipe_sewa') is-invalid @enderror" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="bulanan" {{ old('tipe_sewa') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            <option value="harian" {{ old('tipe_sewa') === 'harian' ? 'selected' : '' }}>Harian</option>
        </select>
        @error('tipe_sewa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <small class="form-text text-muted">Jika memilih Bulanan, tanggal keluar otomatis +30 hari.</small>
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
        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', $occupancy->tanggal_masuk ?? now()->format('Y-m-d')) }}" required readonly>
        @error('tanggal_masuk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <small class="form-text text-muted">Tanggal masuk otomatis diisi hari ini dan tidak dapat diubah.</small>
    </div>

    <div class="form-group mb-3">
        <label for="tanggal_keluar">Tanggal Keluar</label>
        <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror" value="{{ old('tanggal_keluar', $occupancy->tanggal_keluar ?? '') }}">
        @error('tanggal_keluar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $occupancy ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('occupancies.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSewa = document.getElementById('tipe_sewa');
        const tanggalMasuk = document.getElementById('tanggal_masuk');
        const tanggalKeluar = document.getElementById('tanggal_keluar');

        function autoCalculateCheckout() {
            if (!tanggalMasuk.value) return;
            const tipe = tipeSewa.value;
            if (tipe === 'bulanan') {
                const masuk = new Date(tanggalMasuk.value);
                const keluar = new Date(masuk);
                keluar.setDate(keluar.getDate() + 30);
                const year = keluar.getFullYear();
                const month = String(keluar.getMonth() + 1).padStart(2, '0');
                const day = String(keluar.getDate()).padStart(2, '0');
                tanggalKeluar.value = `${year}-${month}-${day}`;
            }
        }

        function autoCalculateCheckin() {
            if (!tanggalKeluar.value) return;
            const tipe = tipeSewa.value;
            if (tipe === 'bulanan') {
                const keluar = new Date(tanggalKeluar.value);
                const masuk = new Date(keluar);
                masuk.setDate(masuk.getDate() - 30);
                const year = masuk.getFullYear();
                const month = String(masuk.getMonth() + 1).padStart(2, '0');
                const day = String(masuk.getDate()).padStart(2, '0');
                tanggalMasuk.value = `${year}-${month}-${day}`;
            }
        }

        tipeSewa.addEventListener('change', function() {
            if (tipeSewa.value !== 'bulanan') {
                // For harian, do not auto fill; leave as-is or user set
                return;
            }
            autoCalculateCheckout();
        });

        tanggalMasuk.addEventListener('change', autoCalculateCheckout);
        tanggalKeluar.addEventListener('change', autoCalculateCheckin);
    });
</script>
