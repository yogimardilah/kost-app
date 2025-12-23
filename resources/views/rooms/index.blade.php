@extends('layouts.app')

@section('title', 'Daftar Kamar')

@section('content_header')
    <h1>Daftar Kamar</h1>
@endsection

@section('content')
    <style>
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .room-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-height: auto;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .room-card-header {
            /* Terisi: biru jelas */
            background: linear-gradient(135deg, #1e90ff 0%, #1c7ed6 100%);
            color: white;
            padding: 12px 15px;
            position: relative;
        }

        .room-card-header.available {
            /* Tersedia: hijau jelas */
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .room-number {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        .room-status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 6px;
            background: rgba(255, 255, 255, 0.3);
        }

        .room-card-body {
            padding: 12px 15px;
            position: relative;
            z-index: 1;
        }

        .room-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .room-info-label {
            font-weight: 500;
            color: #666;
        }

        .room-info-value {
            font-weight: bold;
            color: #333;
        }

        .room-addons {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        .addon-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .room-card-footer {
            padding: 8px 15px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 11px;
        }

        .room-grid-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .search-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
    </style>

    <div class="room-grid-header">
        <div>
            <h2 style="margin: 0;">Daftar Kamar</h2>
            <p style="margin: 5px 0 0 0; color: #999;">Total: {{ $rooms->total() }}</p>
        </div>

        <div style="display:flex; gap:12px; align-items:center;">
            <form class="search-form" method="GET" action="{{ route('rooms.index') }}">
                @if(!empty($kostId))
                    <input type="hidden" name="kost_id" value="{{ $kostId }}">
                @endif
                <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor atau jenis..." style="width:220px;" />
                <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                <a href="{{ route('rooms.index') }}" class="btn btn-light">Reset</a>
            </form>

            <a href="{{ route('rooms.create', $kostId) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kamar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($rooms->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h4>Belum ada kamar</h4>
            <p>Mulai tambahkan kamar untuk kos Anda</p>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary">Tambah Kamar Pertama</a>
        </div>
    @else
        <div class="room-grid">
            @foreach($rooms as $room)
                <div class="room-card">
                    <div class="room-card-header {{ $room->status === 'tersedia' ? 'available' : '' }}">
                        <p class="room-number">{{ $room->nomor_kamar }}</p>
                        <span class="room-status">
                            {{ $room->status === 'tersedia' ? '✓ Tersedia' : '✗ Terisi' }}
                        </span>
                    </div>

                    <div class="room-card-body">
                        <div class="room-info-item">
                            <span class="room-info-label">Jenis:</span>
                            <span class="room-info-value">{{ $room->jenis_kamar }}</span>
                        </div>

                        <div class="room-info-item">
                            <span class="room-info-label">Harga Bulanan:</span>
                            <span class="room-info-value">
                                <i class="fas fa-money-bill-wave"></i> {{ number_format($room->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($room->harga_harian)
                            <div class="room-info-item">
                                <span class="room-info-label">Harga Harian:</span>
                                <span class="room-info-value">
                                    <i class="fas fa-money-bill-wave"></i> {{ number_format($room->harga_harian, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        @if($room->addons->count() > 0)
                            <div class="room-addons">
                                <div style="font-size: 12px; color: #666; margin-bottom: 6px; font-weight: 500;">
                                    <i class="fas fa-star"></i> Fasilitas:
                                </div>
                                @foreach($room->addons as $addon)
                                    <span class="addon-badge">{{ $addon->nama ?? $addon->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="room-card-footer">
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-warning" title="Edit kamar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kamar ini?')" title="Hapus kamar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $rooms->links() }}
        </div>
    @endif

@endsection
