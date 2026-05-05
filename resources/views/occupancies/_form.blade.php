@php
    $occupancy = $occupancy ?? null;
    $selectedRoomId = $selectedRoomId ?? null;
    $isExtending = $isExtending ?? false;
    $action = $occupancy ? route('occupancies.update', $occupancy) : route('occupancies.store');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($occupancy)
        @method('PUT')
    @endif
    
    @if($isExtending)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> <strong>Mode Perpanjangan Sewa</strong><br>
            Tanggal sewa otomatis diatur untuk periode berikutnya. Harga bulanan penuh tanpa prorate.
        </div>
    @endif

    <div class="form-group mb-3">
        <label for="room_id">Kamar <span class="text-danger">*</span></label>
        <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required {{ $isExtending ? 'disabled' : '' }}>
            <option value="">-- Pilih Kamar --</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" 
                    data-jenis="{{ $room->jenis_kamar }}"
                    data-harga="{{ number_format($room->harga, 0, ',', '.') }}"
                    data-harga-harian="{{ $room->harga_harian ? number_format($room->harga_harian, 0, ',', '.') : '-' }}"
                    data-fasilitas="{{ $room->fasilitas ?? '-' }}"
                    {{ old('room_id', $occupancy->room_id ?? $selectedRoomId ?? '') == $room->id ? 'selected' : '' }}>
                    {{ $room->nomor_kamar }} - {{ $room->jenis_kamar }}
                </option>
            @endforeach
        </select>
        @if($isExtending)
            <input type="hidden" name="room_id" value="{{ $occupancy->room_id }}">
        @endif
        @error('room_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <!-- Room Info Display -->
    <div id="room-info" class="alert alert-info" style="display: none;">
        <h6><strong>Informasi Kamar</strong></h6>
        <table class="table table-sm table-borderless mb-0">
            <tr>
                <td width="150"><strong>Jenis Kamar:</strong></td>
                <td id="info-jenis">-</td>
            </tr>
            <tr>
                <td><strong>Harga Bulanan:</strong></td>
                <td id="info-harga">-</td>
            </tr>
            <tr>
                <td><strong>Harga Harian:</strong></td>
                <td id="info-harga-harian">-</td>
            </tr>
            <tr>
                <td><strong>Fasilitas:</strong></td>
                <td id="info-fasilitas" style="white-space: pre-line;">-</td>
            </tr>
        </table>
    </div>

    <div class="form-group mb-3">
        <label for="tipe_sewa">Tipe Sewa <span class="text-danger">*</span></label>
        <select name="tipe_sewa" id="tipe_sewa" class="form-control @error('tipe_sewa') is-invalid @enderror" required {{ $isExtending ? 'disabled' : '' }}>
            <option value="">-- Pilih Tipe --</option>
            <option value="bulanan" {{ old('tipe_sewa', $occupancy->tipe_harga ?? ($isExtending ? 'bulanan' : '')) === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            <option value="harian" {{ old('tipe_sewa', $occupancy->tipe_harga ?? '') === 'harian' ? 'selected' : '' }}>Harian</option>
        </select>
        @if($isExtending)
            <input type="hidden" name="tipe_sewa" value="bulanan">
        @endif
        @error('tipe_sewa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <small class="form-text text-muted">{{ $isExtending ? 'Tipe sewa perpanjangan otomatis Bulanan.' : 'Jika memilih Bulanan, tanggal keluar otomatis +30 hari.' }}</small>
    </div>

    <div class="form-group mb-3">
        <label for="consumer_id">Penyewa <span class="text-danger">*</span></label>
        <select name="consumer_id" id="consumer_id" class="form-control @error('consumer_id') is-invalid @enderror" required {{ $isExtending ? 'disabled' : '' }}>
            <option value="">-- Pilih Penyewa --</option>
            @foreach($consumers as $c)
                <option value="{{ $c->id }}" {{ old('consumer_id', $occupancy->consumer_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->nama }} ({{ $c->nik }})
                </option>
            @endforeach
        </select>
        @if($isExtending)
            <input type="hidden" name="consumer_id" value="{{ $occupancy->consumer_id }}">
        @endif
        @error('consumer_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', $occupancy->tanggal_masuk ?? now()->format('Y-m-d')) }}" required readonly>
        @error('tanggal_masuk') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <small class="form-text text-muted">{{ $isExtending ? 'Tanggal masuk perpanjangan dimulai tanggal 6.' : 'Tanggal masuk otomatis diisi hari ini dan tidak dapat diubah.' }}</small>
    </div>

    <div class="form-group mb-3">
        <label for="tanggal_keluar">Tanggal Keluar</label>
        <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror" value="{{ old('tanggal_keluar', $occupancy->tanggal_keluar ?? '') }}" {{ $isExtending ? 'readonly' : '' }}>
        @error('tanggal_keluar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <small class="form-text text-muted">{{ $isExtending ? 'Tanggal keluar perpanjangan sampai tanggal 5 bulan depan (harga bulanan penuh).' : 'Bulanan: maksimal sampai tanggal 5 periode berikutnya. Harian: boleh pilih tanggal keluar lewat tanggal 5.' }}</small>
    </div>

    <!-- Price Calculation Display -->
    <div id="price-calculation" class="alert alert-warning" style="display: none;">
        <h6><strong id="calc-title">Perhitungan Harga</strong></h6>
        <table class="table table-sm table-borderless mb-0">
            <tr id="row-monthly-price">
                <td width="180"><strong>Harga Bulanan:</strong></td>
                <td id="calc-monthly-price">-</td>
            </tr>
            <tr id="row-daily-base-price" style="display: none;">
                <td width="180"><strong>Harga Harian:</strong></td>
                <td id="calc-daily-base-price">-</td>
            </tr>
            <tr>
                <td><strong>Jumlah Hari:</strong></td>
                <td id="calc-days">-</td>
            </tr>
            <tr>
                <td><strong>Harga per Hari:</strong></td>
                <td id="calc-daily-rate">-</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td id="calc-total" style="font-weight: bold; color: #e74c3c;">-</td>
            </tr>
        </table>
    </div>

    <div class="form-group">
        @if($isExtending)
            <input type="hidden" name="is_extending" value="1">
        @endif
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $isExtending ? 'Simpan Perpanjangan' : ($occupancy ? 'Update' : 'Simpan') }}
        </button>
        <a href="{{ route('occupancies.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSewa = document.getElementById('tipe_sewa');
        const tanggalMasuk = document.getElementById('tanggal_masuk');
        const tanggalKeluar = document.getElementById('tanggal_keluar');
        const roomSelect = document.getElementById('room_id');
        const roomInfo = document.getElementById('room-info');
        const priceCalc = document.getElementById('price-calculation');
        const isExtending = {{ $isExtending ? 'true' : 'false' }};

        // Show room info when room is selected
        function updateRoomInfo() {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            if (roomSelect.value && selectedOption) {
                const jenis = selectedOption.getAttribute('data-jenis');
                const harga = selectedOption.getAttribute('data-harga');
                const hargaHarian = selectedOption.getAttribute('data-harga-harian');
                const fasilitas = selectedOption.getAttribute('data-fasilitas');

                document.getElementById('info-jenis').textContent = jenis || '-';
                document.getElementById('info-harga').textContent = harga ? 'Rp ' + harga : '-';
                document.getElementById('info-harga-harian').textContent = hargaHarian !== '-' ? 'Rp ' + hargaHarian : '-';
                document.getElementById('info-fasilitas').textContent = fasilitas || '-';
                
                roomInfo.style.display = 'block';
                
                // Don't show price calculation if extending
                if (!isExtending) {
                    calculatePriceProration();
                }
            } else {
                roomInfo.style.display = 'none';
                priceCalc.style.display = 'none';
            }
        }

        // Calculate and display price calculation for both monthly and daily rental
        function calculatePriceProration() {
            // Skip calculation if extending mode
            if (isExtending) {
                priceCalc.style.display = 'none';
                return;
            }
            
            const tipe = tipeSewa.value;
            if (!tipe || !tanggalMasuk.value || !tanggalKeluar.value || !roomSelect.value) {
                priceCalc.style.display = 'none';
                return;
            }

            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const hargaStr = selectedOption.getAttribute('data-harga');
            const hargaHarianStr = selectedOption.getAttribute('data-harga-harian');
            const hargaBulanan = parseFloat(hargaStr.replace(/\./g, ''));
            const hargaHarian = hargaHarianStr !== '-' ? parseFloat(hargaHarianStr.replace(/\./g, '')) : 0;

            const masuk = new Date(tanggalMasuk.value);
            const keluar = new Date(tanggalKeluar.value);
            const diffTime = Math.abs(keluar - masuk);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays <= 0) {
                priceCalc.style.display = 'none';
                return;
            }

            if (tipe === 'bulanan') {
                // Calculate actual days in the running month
                const year = masuk.getFullYear();
                const month = masuk.getMonth();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                if (hargaBulanan > 0) {
                    const hargaPerHari = hargaBulanan / daysInMonth;
                    const totalProrate = hargaPerHari * diffDays;
                    const totalProrateRounded = Math.round(totalProrate / 100) * 100;

                    document.getElementById('calc-title').textContent = 'Perhitungan Harga (Bulanan - Prorate)';
                    document.getElementById('row-monthly-price').style.display = '';
                    document.getElementById('row-daily-base-price').style.display = 'none';
                    document.getElementById('calc-monthly-price').textContent = 'Rp ' + hargaBulanan.toLocaleString('id-ID');
                    document.getElementById('calc-days').textContent = diffDays + ' hari (dari ' + daysInMonth + ' hari)';
                    document.getElementById('calc-daily-rate').textContent = 'Rp ' + Math.round(hargaPerHari).toLocaleString('id-ID');
                    document.getElementById('calc-total').textContent = 'Rp ' + totalProrateRounded.toLocaleString('id-ID');
                    
                    priceCalc.style.display = 'block';
                } else {
                    priceCalc.style.display = 'none';
                }
            } else if (tipe === 'harian') {
                if (hargaHarian > 0) {
                    const totalHarian = hargaHarian * diffDays;
                    const totalHarianRounded = Math.round(totalHarian / 100) * 100;

                    document.getElementById('calc-title').textContent = 'Perhitungan Harga (Harian)';
                    document.getElementById('row-monthly-price').style.display = 'none';
                    document.getElementById('row-daily-base-price').style.display = '';
                    document.getElementById('calc-daily-base-price').textContent = 'Rp ' + hargaHarian.toLocaleString('id-ID');
                    document.getElementById('calc-days').textContent = diffDays + ' hari';
                    document.getElementById('calc-daily-rate').textContent = 'Rp ' + hargaHarian.toLocaleString('id-ID');
                    document.getElementById('calc-total').textContent = 'Rp ' + totalHarianRounded.toLocaleString('id-ID');
                    
                    priceCalc.style.display = 'block';
                } else {
                    priceCalc.style.display = 'none';
                }
            } else {
                priceCalc.style.display = 'none';
            }
        }

        // Validate checkout date doesn't exceed 5th of next applicable month
        function validateCheckoutDate() {
            if (!tanggalKeluar.value || !tanggalMasuk.value) return true;

            // Daily rental is not capped by day-5 cycle.
            if (tipeSewa.value !== 'bulanan') {
                return true;
            }
            
            const masuk = new Date(tanggalMasuk.value);
            const keluar = new Date(tanggalKeluar.value);
            
            // Calculate max checkout: 5th of next applicable month
            let maxCheckout;
            if (masuk.getDate() < 5) {
                // If check-in before 5th, max is 5th of same month
                maxCheckout = new Date(masuk.getFullYear(), masuk.getMonth(), 5);
            } else {
                // If check-in on/after 5th, max is 5th of next month
                maxCheckout = new Date(masuk.getFullYear(), masuk.getMonth() + 1, 5);
            }
            
            // Compare dates only (ignore time component)
            const keluarDate = new Date(keluar.getFullYear(), keluar.getMonth(), keluar.getDate());
            const maxDate = new Date(maxCheckout.getFullYear(), maxCheckout.getMonth(), maxCheckout.getDate());
            
            if (keluarDate > maxDate) {
                const maxStr = maxCheckout.toLocaleDateString('id-ID');
                alert('Tanggal keluar tidak boleh melewati ' + maxStr + '!');
                tanggalKeluar.value = '';
                return false;
            }
            return true;
        }

        roomSelect.addEventListener('change', updateRoomInfo);
        
        // Trigger on page load if room is pre-selected
        if (roomSelect.value) {
            updateRoomInfo();
        }

        function autoCalculateCheckout() {
            if (!tanggalMasuk.value) return;
            const tipe = tipeSewa.value;
            if (tipe === 'bulanan') {
                const masuk = new Date(tanggalMasuk.value);
                
                // Calculate max checkout: 5th of next applicable month
                let maxCheckout;
                if (masuk.getDate() < 5) {
                    // If check-in before 5th, max is 5th of same month
                    maxCheckout = new Date(masuk.getFullYear(), masuk.getMonth(), 5);
                } else {
                    // If check-in on/after 5th, max is 5th of next month
                    maxCheckout = new Date(masuk.getFullYear(), masuk.getMonth() + 1, 5);
                }
                
                const year = maxCheckout.getFullYear();
                const month = String(maxCheckout.getMonth() + 1).padStart(2, '0');
                const day = String(maxCheckout.getDate()).padStart(2, '0');
                tanggalKeluar.value = `${year}-${month}-${day}`;
                calculatePriceProration();
            }
        }

        tipeSewa.addEventListener('change', function() {
            if (tipeSewa.value === 'bulanan') {
                autoCalculateCheckout();
            } else {
                calculatePriceProration();
            }
        });

        tanggalMasuk.addEventListener('change', function() {
            if (tipeSewa.value === 'bulanan') {
                autoCalculateCheckout();
            } else {
                calculatePriceProration();
            }
        });
        
        tanggalKeluar.addEventListener('change', function() {
            if (validateCheckoutDate()) {
                calculatePriceProration();
            }
        });
    });
</script>
