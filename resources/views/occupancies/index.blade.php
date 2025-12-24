@extends('layouts.app')

@section('title', 'Seat Map - Kamar Kost')

@section('content_header')
    <!-- <h1>Seat Map - Status Kamar</h1> -->
@endsection

@section('content')
    <style>
        .seat-map-container {
            background-color: #2b333bff; /* dark slate */
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(66, 55, 55, 0.45);
        }




        .cinema-screen {
            background: linear-gradient(to bottom, #1a1a2e 0%, #16213e 100%);
            border-radius: 10px 10px 50% 50%;
            padding: 15px;
            text-align: center;
            color: #fff;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
            margin-top: 16px;
        }

        .seat-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            position: relative;
            height: 78px;
        }

        .seat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .seat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
        }

        .seat-card.available::before { color: #28a745; }
        .seat-card.occupied::before { color: #007bff; }
        .seat-card.warning::before { color: #dc3545; }
        .seat-card.soon::before { color: #f0ad4e; }
        .seat-card.expired::before { color: #6c757d; }

        .seat-header {
            padding: 10px 8px;
            color: #fff;
            position: relative;
            height: 78px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .seat-header.available {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .seat-header.occupied {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .seat-header.warning {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .seat-header.soon {
            background: linear-gradient(135deg, #f8c146 0%, #f0ad4e 100%);
        }

        .seat-header.expired {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .seat-number {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            text-align: center;
        }

        .seat-tenant {
            font-size: 9px;
            opacity: 0.85;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70px;
            text-align: center;
        }

        .seat-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            padding: 2px 5px;
            border-radius: 9px;
            font-size: 7px;
            font-weight: 700;
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }

        .seat-body {
            display: none;
        }

        .seat-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            color: #495057;
        }

        .seat-info-row .label {
            font-weight: 600;
        }

        .seat-info-row .value {
            font-weight: 400;
        }

        .days-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .days-badge.danger { background: #fff3cd; color: #856404; }
        .days-badge.success { background: #d1ecf1; color: #0c5460; }

        .legend-container {
            display: flex;
            gap: 14px;
            justify-content: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 18px;
            backdrop-filter: blur(10px);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .legend-box.available { background: linear-gradient(135deg, #28a745, #20c997); }
        .legend-box.occupied { background: linear-gradient(135deg, #007bff, #0056b3); }
        .legend-box.warning { background: linear-gradient(135deg, #dc3545, #c82333); }
        .legend-box.soon { background: linear-gradient(135deg, #f8c146, #f0ad4e); }
        .legend-box.expired { background: linear-gradient(135deg, #6c757d, #495057); }

        .stats-row {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .stat-box {
            flex: 1;
            min-width: 130px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            text-align: center;
        }

        .stat-number {
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Modal */
        .seat-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .seat-modal {
            background: #fff;
            border-radius: 15px;
            width: 600px;
            max-width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.3s;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .seat-modal-header {
            padding: 20px;
            color: #fff;
            position: relative;
        }

        .seat-modal-header.available { background: linear-gradient(135deg, #28a745, #20c997); }
        .seat-modal-header.occupied { background: linear-gradient(135deg, #007bff, #0056b3); }
        .seat-modal-header.warning { background: linear-gradient(135deg, #dc3545, #c82333); }
        .seat-modal-header.soon { background: linear-gradient(135deg, #f8c146, #f0ad4e); }
        .seat-modal-header.expired { background: linear-gradient(135deg, #6c757d, #495057); }

        .seat-modal-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
        }

        .seat-modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            border: none;
            background: rgba(255,255,255,0.3);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            color: #fff;
            transition: all 0.3s;
        }

        .seat-modal-close:hover {
            background: rgba(255,255,255,0.5);
            transform: rotate(90deg);
        }

        .seat-modal-body {
            padding: 25px;
        }

        .modal-info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .modal-info-row:last-child {
            border-bottom: none;
        }

        .modal-info-row .label {
            font-weight: 600;
            color: #666;
        }

        .modal-info-row .value {
            font-weight: 500;
            color: #333;
        }

        .seat-modal-actions {
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cinema {
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cinema:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        @media (max-width: 768px) {
            .seat-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 8px;
            }
            
            .seat-card {
                height: 85px;
            }
            
            .seat-number {
                font-size: 22px;
            }
            
            .seat-tenant {
                font-size: 9px;
            }
        }


        .search-bar {
    background-color: #4b5258;
    padding: 10px 22px;
    border-radius: 16px;
    margin-bottom: 18px;
}

.search-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* BARIS ATAS */
.search-main {
    display: flex;
    align-items: center;
    gap: 12px;
}

.search-input {
    flex: 1;
    border-radius: 12px;
    height: 42px;
}

/* GRUP TOMBOL */
.search-actions {
    display: flex;
    align-items: stretch;
    gap: 8px;
}

.search-actions .btn {
    display: flex;
    align-items: center;
    justify-content: center;

    height: 40px;          /* SAMA dengan input */
    line-height: 40px;     /* INI KUNCINYA */
    padding: 0 16px;

    border-radius: 10px;
    white-space: nowrap;
}


/* BARIS BAWAH */
.search-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #e5e7eb;
}

.search-footer select {
    width: 70px;
    border-radius: 8px;
}

    </style>

    <div class="seat-map-container">
        <!-- Cinema Screen -->
        <div class="cinema-screen">
            🏠 KOST MANAGEMENT SYSTEM
        </div>

        <!-- Search & Stats -->
<div class="search-bar">
    <form method="GET" action="{{ route('occupancies.index') }}" class="search-form">

        <!-- BARIS UTAMA -->
        <div class="search-main">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control search-input"
                placeholder="Cari nomor kamar atau nama penyewa"
            >

            <div class="search-actions">
                <button type="submit" class="btn btn-primary">Cari</button>

                <a href="{{ route('occupancies.create') }}" class="btn btn-success">
                    + Tambah
                </a>
            </div>
            <span  class="search-footer">Per halaman</span>
            <select
                name="per_page"
                class="form-select form-select-sm"
                onchange="this.form.submit()"
            >
                <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100</option>
                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
            </select>
        </div>

    </form>
</div>


        <!-- Statistics -->
        @php
            $totalRooms = $paginatedOccupancies->total();
            $occupied = count($occupancies);
            $available = count($availableRooms);
            $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0;
        @endphp

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number">{{ $totalRooms }}</div>
                <div class="stat-label">Total Kamar</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $occupied }}</div>
                <div class="stat-label">Terisi</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $available }}</div>
                <div class="stat-label">Tersedia</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $occupancyRate }}%</div>
                <div class="stat-label">Okupansi</div>
            </div>
        </div>

        <!-- Legend -->
        <div class="legend-container">
            <div class="legend-item">
                <div class="legend-box available"></div>
                <span>Tersedia</span>
            </div>
            <div class="legend-item">
                <div class="legend-box occupied"></div>
                <span>Terisi</span>
            </div>
            <div class="legend-item">
                <div class="legend-box warning"></div>
                <span>Belum Bayar H - 5 Checkout</span>
            </div>
            <div class="legend-item">
                <div class="legend-box soon"></div>
                <span>H - 5 Checkout (Lunas)</span>
            </div>
            <!-- <div class="legend-item">
                <div class="legend-box expired"></div>
                <span>Tidak Aktif</span>
            </div> -->
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Seat Map Grid -->
        <div class="seat-grid">
            <!-- All Rooms (Occupied and Available) -->
            @foreach($paginatedOccupancies as $item)
                @if($item->status === 'available')
                    {{-- Available Room --}}
                    <div class="seat-card available" onclick="openSeatModal(this)"
                         data-type="available"
                         data-class="available"
                         data-edit-url="{{ route('rooms.edit', $item->room) }}"
                         data-add-url="{{ route('occupancies.create', ['room_id' => $item->room->id]) }}"
                         data-room="{{ $item->room->nomor_kamar }}"
                         data-tenant="-"
                         data-status="{{ $item->room->status }}"
                         data-jenis="{{ $item->room->jenis_kamar }}"
                         data-harga="{{ number_format($item->room->harga,0,',','.') }}"
                         data-harga-harian="{{ $item->room->harga_harian ? number_format($item->room->harga_harian,0,',','.') : '-' }}"
                         data-fasilitas="{{ $item->room->fasilitas ?? '-' }}">
                        <div class="seat-header available">
                            <span class="seat-badge">○</span>
                            <div class="seat-number">{{ $item->room->nomor_kamar }}</div>
                            <div class="seat-tenant">{{ Str::limit($item->room->jenis_kamar, 12) }}</div>
                        </div>
                    </div>
                @else
                    {{-- Occupied Room --}}
                    @php
                        $occ = $item;
                        $cardClass = 'occupied';
                        if (!empty($occ->expired)) {
                            $cardClass = 'expired';
                        } elseif (!empty($occ->due_soon_unpaid)) {
                            $cardClass = 'warning';
                        } elseif (!empty($occ->due_soon)) {
                            $cardClass = 'soon';
                        }
                    @endphp

                    <div class="seat-card {{ $cardClass }}" onclick="openSeatModal(this)" 
                         data-type="occupancy"
                         data-class="{{ $cardClass }}"
                         data-edit-url="{{ route('occupancies.edit', $occ) }}"
                         data-billing-url="{{ $occ->billing_url ?? '' }}"
                         data-billing-status="{{ $occ->billing_status ?? '' }}"
                         data-billing-invoice="{{ $occ->billing_invoice ?? '' }}"
                         data-billing-total="{{ $occ->billing_total ?? '' }}"
                         data-billing-remaining="{{ $occ->billing_remaining ?? '' }}"
                         data-consumer-phone="{{ $occ->consumer->no_hp ?? '' }}"
                         data-consumer-id="{{ $occ->consumer->id ?? '' }}"
                         data-complete-url="{{ $occ->complete_url ?? '' }}"
                         data-upgrade-url="{{ $occ->upgrade_url ?? '' }}"
                         data-room="{{ $occ->room->nomor_kamar ?? '-' }}"
                         data-jenis="{{ $occ->room->jenis_kamar ?? '-' }}"
                         data-fasilitas="{{ $occ->room->fasilitas ?? '-' }}"
                         data-tenant="{{ $occ->consumer->nama ?? '-' }}"
                         data-status="{{ $occ->status }}"
                         data-masuk="{{ $occ->tanggal_masuk }}"
                         data-keluar="{{ $occ->tanggal_keluar }}"
                         data-days="{{ is_null($occ->days_remaining) ? '' : $occ->days_remaining }}">
                        <div class="seat-header {{ $cardClass }}">
                            <span class="seat-badge">
                                @if(!empty($occ->expired))
                                    ✖
                                @elseif(!empty($occ->due_soon_unpaid))
                                    !
                                @else
                                    ✓
                                @endif
                            </span>
                            <div class="seat-number">{{ $occ->room->nomor_kamar ?? '-' }}</div>
                            <div class="seat-tenant">{{ Str::limit($occ->consumer->nama ?? '-', 12) }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $paginatedOccupancies->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="seat-modal-backdrop" id="seatModal" onclick="if(event.target === this) closeSeatModal()">
        <div class="seat-modal">
            <div class="seat-modal-header occupied" id="modalHeader">
                <button class="seat-modal-close" onclick="closeSeatModal()">&times;</button>
                <div class="seat-modal-title" id="modalRoom">-</div>
                <div style="font-size: 14px; margin-top: 5px; opacity: 0.9;" id="modalTenant">-</div>
            </div>
            <div class="seat-modal-body" id="modalBody">
                <!-- Dynamic content -->
            </div>
            <div class="seat-modal-actions">
                <a id="modalAdd" class="btn btn-success btn-cinema" href="#" style="display:none;">Tambah Penyewa</a>
                <a id="modalAddons" class="btn btn-warning btn-cinema" href="#" style="display:none;">Tambah Addons</a>
                <a id="modalBilling" class="btn btn-info btn-cinema" href="#" style="display:none;">Billing</a>
                <a id="modalUpgrade" class="btn btn-primary btn-cinema" href="#" style="display:none;">Upgrade Kamar</a>
                <a id="modalWhatsApp" class="btn btn-success btn-cinema" href="#" target="_blank" style="display:none;">
                    <i class="fab fa-whatsapp"></i> Kirim WA
                </a>
                <form id="modalCompleteForm" method="POST" style="display:none;">
                    @csrf
                </form>
                <button id="modalComplete" class="btn btn-success btn-cinema" type="button" style="display:none;" onclick="completeOccupancy()">Selesai Sewa</button>
                <a id="modalEdit" class="btn btn-primary btn-cinema" href="#">Edit</a>
                <button class="btn btn-secondary btn-cinema" type="button" onclick="closeSeatModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function closeSeatModal() {
            document.getElementById('seatModal').style.display = 'none';
        }

        function completeOccupancy() {
            if (confirm('Yakin ingin menyelesaikan penyewaan ini? Kamar akan menjadi tersedia untuk penyewa baru.')) {
                document.getElementById('modalCompleteForm').submit();
            }
        }

        function isOwner() {
            return {{ auth()->user()->role_id === 1 ? 'true' : 'false' }};
        }

        // Owner (1) or Admin (2) can upgrade
        function isAdminOrOwner() {
            return {{ in_array(auth()->user()->role_id, [1,2]) ? 'true' : 'false' }};
        }

        function openSeatModal(card) {
            const modal = document.getElementById('seatModal');
            const header = document.getElementById('modalHeader');
            const body = document.getElementById('modalBody');
            
            const data = card.dataset;
            const cardClass = data.class || 'occupied';
            
            header.className = 'seat-modal-header ' + cardClass;
            document.getElementById('modalRoom').textContent = data.room;
            document.getElementById('modalTenant').textContent = data.tenant;
            
            // Build modal body
            let bodyHTML = '';
            
            if (data.type === 'available') {
                bodyHTML = `
                    <div class="modal-info-row"><span class="label">Status</span><span class="value">${data.status}</span></div>
                    <div class="modal-info-row"><span class="label">Jenis</span><span class="value">${data.jenis}</span></div>
                    <div class="modal-info-row"><span class="label">Harga Bulanan</span><span class="value">Rp ${data.harga}</span></div>
                    <div class="modal-info-row"><span class="label">Harga Harian</span><span class="value">Rp ${data.hargaHarian}</span></div>
                    <div class="modal-info-row"><span class="label">Fasilitas</span><span class="value" style="white-space: pre-line;">${data.fasilitas || '-'}</span></div>
                `;
                
                document.getElementById('modalAdd').style.display = '';
                document.getElementById('modalAdd').href = data.addUrl;
                document.getElementById('modalEdit').style.display = 'none';
                document.getElementById('modalAddons').style.display = 'none';
                document.getElementById('modalBilling').style.display = 'none';
                document.getElementById('modalWhatsApp').style.display = 'none';
                document.getElementById('modalComplete').style.display = 'none';
            } else {
                bodyHTML = `
                    <div class="modal-info-row"><span class="label">Penyewa</span><span class="value">${data.tenant}</span></div>
                    <div class="modal-info-row"><span class="label">Jenis Kamar</span><span class="value">${data.jenis || '-'}</span></div>
                    <div class="modal-info-row"><span class="label">Status</span><span class="value">${data.status}</span></div>
                    <div class="modal-info-row"><span class="label">Check-in</span><span class="value">${data.masuk}</span></div>
                    <div class="modal-info-row"><span class="label">Check-out</span><span class="value">${data.keluar || '-'}</span></div>
                    <div class="modal-info-row"><span class="label">Fasilitas</span><span class="value" style="white-space: pre-line;">${data.fasilitas || '-'}</span></div>
                `;
                
                if (data.days) {
                    bodyHTML += `<div class="modal-info-row"><span class="label">Sisa Waktu</span><span class="value">${data.days} hari</span></div>`;
                }
                
                // Show billing remaining if there's unpaid billing
                if (data.billingRemaining && parseFloat(data.billingRemaining) > 0) {
                    const billingStatus = data.billingStatus || '';
                    const statusLabel = billingStatus.toLowerCase() === 'sebagian' ? 'Belum Lunas' : billingStatus;
                    bodyHTML += `<div class="modal-info-row"><span class="label">Status Tagihan</span><span class="value" style="color: #dc3545;">${statusLabel}</span></div>`;
                    bodyHTML += `<div class="modal-info-row"><span class="label">Sisa Tagihan</span><span class="value" style="color: #dc3545; font-weight: bold;">Rp ${parseFloat(data.billingRemaining || 0).toLocaleString('id-ID')}</span></div>`;
                }
                                
                document.getElementById('modalAdd').style.display = 'none';
                
                // Edit: only Owner (role_id = 1)
                if (isOwner()) {
                    document.getElementById('modalEdit').style.display = '';
                    document.getElementById('modalEdit').href = data.editUrl;
                } else {
                    document.getElementById('modalEdit').style.display = 'none';
                }

                // Upgrade: Owner (1) and Admin (2)
                if (isAdminOrOwner() && data.upgradeUrl) {
                    document.getElementById('modalUpgrade').style.display = '';
                    document.getElementById('modalUpgrade').href = data.upgradeUrl;
                } else {
                    document.getElementById('modalUpgrade').style.display = 'none';
                }
                
                // Show Addons button for occupied rooms
                document.getElementById('modalAddons').style.display = '';
                document.getElementById('modalAddons').href = '/addon-transactions/create?consumer_id=' + data.consumerId;
                
                // Check if billing exists: has any billing status set
                const hasBilling = !!data.billingStatus;
                const paid = (data.billingStatus || '').toLowerCase() === 'lunas';
                const phoneRaw = data.consumerPhone || '';
                const phone = phoneRaw ? phoneRaw.replace(/^0/, '62').replace(/[^0-9]/g, '') : '';

                if (hasBilling && !paid && data.billingUrl) {
                    // Has unpaid billing - show billing button
                    document.getElementById('modalBilling').style.display = '';
                    document.getElementById('modalBilling').href = data.billingUrl;
                    
                    // Show WhatsApp button if unpaid billing exists and consumer has phone
                    if (phone && data.billingInvoice && data.billingRemaining) {
                        const daysInfo = data.days ? `Checkout dalam ${data.days} hari.\n` : '';
                        const message = encodeURIComponent(
                            `Halo ${data.tenant},\n\n` +
                            `Informasi tagihan kost Anda:\n` +
                            `Invoice: ${data.billingInvoice}\n` +
                            `Total Tagihan: Rp ${parseFloat(data.billingTotal || 0).toLocaleString('id-ID')}\n` +
                            `Sisa Tagihan: Rp ${parseFloat(data.billingRemaining || 0).toLocaleString('id-ID')}\n` +
                            `${daysInfo}\n` +
                            `Mohon segera melakukan pembayaran. Terima kasih.`
                        );
                        const waUrl = `https://wa.me/${phone}?text=${message}`;
                        document.getElementById('modalWhatsApp').style.display = '';
                        document.getElementById('modalWhatsApp').href = waUrl;
                    } else {
                        document.getElementById('modalWhatsApp').style.display = 'none';
                    }
                } else {
                    document.getElementById('modalBilling').style.display = 'none';

                    // If sudah lunas atau tidak ada tagihan: kirim ajakan booking/perpanjang
                    if (phone) {
                        const statusInfo = !hasBilling ? 'sedang tidak ada tagihan aktif' : 'sudah lunas';
                        const daysInfo = data.days ? `Checkout dalam ${data.days} hari.` : '';
                        const message = encodeURIComponent(
                            `Halo ${data.tenant},\n\n` +
                            `Status tagihan Anda ${statusInfo}. ${daysInfo} Jika ingin booking/perpanjang untuk periode berikutnya (bulanan atau harian), silakan konfirmasi.\n` +
                            `Kamar: ${data.room || '-'}\n` +
                            `Terima kasih.`
                        );
                        const waUrl = `https://wa.me/${phone}?text=${message}`;
                        document.getElementById('modalWhatsApp').style.display = '';
                        document.getElementById('modalWhatsApp').href = waUrl;
                    } else {
                        document.getElementById('modalWhatsApp').style.display = 'none';
                    }
                }
                
                // Show complete button if: (no billing OR paid) AND has complete URL
                const canComplete = (!hasBilling || paid) && data.completeUrl;
                if (canComplete) {
                    document.getElementById('modalComplete').style.display = '';
                    document.getElementById('modalCompleteForm').action = data.completeUrl;
                } else {
                    document.getElementById('modalComplete').style.display = 'none';
                }
            }
            
            body.innerHTML = bodyHTML;
            modal.style.display = 'flex';
        }
    </script>

@endsection
