@extends('layouts.app')

@section('title', 'Upgrade Kamar')

@section('content_header')
    <h1>Upgrade Kamar</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <div><strong>Penyewa:</strong> {{ $occupancy->consumer->nama ?? '-' }}</div>
                <div><strong>Kamar Saat Ini:</strong> {{ $occupancy->room->nomor_kamar ?? '-' }}</div>
                <div><strong>Harga Saat Ini:</strong> Bulanan Rp {{ number_format($occupancy->room->harga ?? 0,0,',','.') }} | Harian Rp {{ number_format($occupancy->room->harga_harian ?? 0,0,',','.') }}</div>
                <div><strong>Tipe Sewa Saat Ini:</strong> {{ $rentType ?? '-' }}</div>
                <div><strong>Periode Tagihan:</strong>
                    @if($billing)
                        {{ optional($billing->periode_awal)->format('d/m/Y') }} s/d {{ optional($billing->periode_akhir)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </div>
                @if($billingSummary)
                    <div class="mt-2">
                        <strong>Transaksi Aktif:</strong><br>
                        Invoice: {{ $billingSummary['invoice'] }}<br>
                        Status: {{ ucfirst($billingSummary['status']) }}<br>
                        Total: Rp {{ number_format($billingSummary['total'] ?? 0,0,',','.') }}<br>
                        Terbayar: Rp {{ number_format($billingSummary['paid'] ?? 0,0,',','.') }}<br>
                        Sisa: Rp {{ number_format($billingSummary['remaining'] ?? 0,0,',','.') }}
                    </div>
                @endif
                <small class="text-muted">Upgrade tidak membuat transaksi baru, selisih harga ditagihkan di invoice berjalan.</small>
            </div>
            <form method="POST" action="{{ route('occupancies.apply-upgrade', $occupancy) }}">
                @csrf
                <input type="hidden" name="rent_type" value="{{ $rentType }}">
                <div class="form-group">
                    <label for="upgrade_from">Tanggal Mulai Harga Baru</label>
                    <input type="date" name="upgrade_from" id="upgrade_from" class="form-control @error('upgrade_from') is-invalid @enderror" 
                           value="{{ old('upgrade_from', optional($billing->periode_awal)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    @error('upgrade_from') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <small class="text-muted">Tanggal mulai berlaku harga kamar baru</small>
                </div>

                <div class="form-group">
                    <label for="upgrade_to">Tanggal Akhir Harga Baru</label>
                    <input type="date" name="upgrade_to" id="upgrade_to" class="form-control @error('upgrade_to') is-invalid @enderror" 
                           value="{{ old('upgrade_to', optional($billing->periode_akhir)->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d')) }}" required>
                    @error('upgrade_to') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <small class="text-muted">Tanggal berakhir harga kamar baru</small>
                </div>

                <div id="pricePreview" class="alert alert-info" style="display:none;">
                    <strong>Perkiraan Selisih Harga:</strong><br>
                    <span id="previewText"></span>
                </div>

                <div class="form-group">
                    <label for="room_id">Pilih Kamar Baru</label>
                    <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->nomor_kamar }} - {{ $room->jenis_kamar }} (Bulanan: Rp {{ number_format($room->harga ?? 0,0,',','.') }}, Harian: Rp {{ number_format($room->harga_harian ?? 0,0,',','.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Proses Upgrade</button>
                <a href="{{ route('occupancies.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const upgradeFrom = document.getElementById('upgrade_from');
    const upgradeTo = document.getElementById('upgrade_to');
    const roomSelect = document.getElementById('room_id');
    const pricePreview = document.getElementById('pricePreview');
    const previewText = document.getElementById('previewText');

    const currentRoomPrice = {{ $occupancy->room->harga ?? 0 }};
    const currentRoomHarianPrice = {{ $occupancy->room->harga_harian ?? 0 }};
    const rentType = '{{ $rentType ?? "-" }}';
    const roomsData = {!! json_encode($rooms->map(function($r) { return ['id' => $r->id, 'nomor' => $r->nomor_kamar, 'harga' => $r->harga, 'harian' => $r->harga_harian]; })->toArray()) !!};

    function calculatePreview() {
        const fromDate = new Date(upgradeFrom.value);
        const toDate = new Date(upgradeTo.value);

        if (!upgradeFrom.value || !upgradeTo.value || !roomSelect.value) {
            pricePreview.style.display = 'none';
            return;
        }

        const days = Math.max(1, Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24)));
        const newRoomId = roomSelect.value;
        const newRoom = roomsData.find(r => r.id == newRoomId);

        let oldTotal, newTotal, oldBreakdown, newBreakdown;

        if (rentType === 'Bulanan') {
            // Always use monthly logic for old room
            if (days <= 30) {
                oldTotal = currentRoomPrice;
                oldBreakdown = `Rp ${currentRoomPrice.toLocaleString('id-ID')} (bulanan, ${days} hari)`;
            } else {
                const remainingDays = days - 30;
                oldTotal = currentRoomPrice + (currentRoomHarianPrice * remainingDays);
                oldBreakdown = `Rp ${currentRoomPrice.toLocaleString('id-ID')} (bulanan) + Rp ${currentRoomHarianPrice.toLocaleString('id-ID')}/hari × ${remainingDays} hari`;
            }

            // Determine new room type based on days
            if (days <= 30) {
                const newMonthlyUnit = newRoom.harga || 0;
                newTotal = newMonthlyUnit;
                newBreakdown = `Rp ${newMonthlyUnit.toLocaleString('id-ID')} (bulanan, ${days} hari)`;
            } else {
                const remainingDays = days - 30;
                const newMonthlyUnit = newRoom.harga || 0;
                const newDailyUnit = newRoom.harian || 0;
                newTotal = newMonthlyUnit + (newDailyUnit * remainingDays);
                newBreakdown = `Rp ${newMonthlyUnit.toLocaleString('id-ID')} (bulanan) + Rp ${newDailyUnit.toLocaleString('id-ID')}/hari × ${remainingDays} hari`;
            }
        } else {
            // Harian: always use daily pricing
            const oldDailyUnit = currentRoomHarianPrice;
            const newDailyUnit = newRoom.harian || 0;
            oldTotal = oldDailyUnit * days;
            newTotal = newDailyUnit * days;
            oldBreakdown = `Rp ${oldDailyUnit.toLocaleString('id-ID')}/hari × ${days} hari`;
            newBreakdown = `Rp ${newDailyUnit.toLocaleString('id-ID')}/hari × ${days} hari`;
        }

        const delta = newTotal - oldTotal;

        let previewHTML = `<div style="margin-bottom: 10px;"><strong>Periode: ${days} hari (Tipe: ${rentType})</strong> (${upgradeFrom.value} s/d ${upgradeTo.value})</div>`;
        previewHTML += `<div style="margin-bottom: 8px;"><strong>Harga Lama:</strong> ${oldBreakdown}<br><span style="margin-left: 20px;">= Rp ${oldTotal.toLocaleString('id-ID')}</span></div>`;
        previewHTML += `<div style="margin-bottom: 8px;"><strong>Harga Baru:</strong> ${newBreakdown}<br><span style="margin-left: 20px;">= Rp ${newTotal.toLocaleString('id-ID')}</span></div>`;
        if (delta > 0) {
            previewHTML += `<div style="color: #dc3545;"><strong>Selisih Lebih: Rp ${delta.toLocaleString('id-ID')}</strong></div>`;
        } else if (delta < 0) {
            previewHTML += `<div style="color: #28a745;"><strong>Selisih Kurang (Kredit): Rp ${Math.abs(delta).toLocaleString('id-ID')}</strong></div>`;
        } else {
            previewHTML += `<div><strong>Tidak ada selisih harga</strong></div>`;
        }

        previewText.innerHTML = previewHTML;
        pricePreview.style.display = 'block';
    }

    upgradeFrom.addEventListener('change', calculatePreview);
    upgradeTo.addEventListener('change', calculatePreview);
    roomSelect.addEventListener('change', calculatePreview);

    // Initial preview
    calculatePreview();
});
</script>
@endpush
