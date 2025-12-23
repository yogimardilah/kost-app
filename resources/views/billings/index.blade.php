@extends('layouts.app')

@section('title','Daftar Tagihan')

@section('content_header')
<h1>Daftar Tagihan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="form-inline mb-3" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Cari invoice/penyewa">
            <select name="status" class="form-control mr-2">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="sebagian" {{ request('status')=='sebagian' ? 'selected' : '' }}>Sebagian</option>
                <option value="lunas" {{ request('status')=='lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control mr-2">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control mr-2">
            <button class="btn btn-primary">Filter</button>
        </form>

        <div class="mb-3">
            <a href="{{ route('billings.reminders') }}" class="btn btn-warning">
                <i class="fas fa-bell"></i> Lihat Reminder (@php echo \App\Models\BillingReminder::where('is_sent', false)->count() @endphp)
            </a>
        </div>

        @if($billings->isEmpty())
            <div class="alert alert-info">Belum ada tagihan.</div>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billings as $i => $b)
                    @php
                        $reminder = \App\Models\BillingReminder::where('billing_id', $b->id)->where('is_sent', false)->first();
                    @endphp
                    <tr>
                        <td>{{ $billings->firstItem() + $i }}</td>
                        <td>
                            <a href="{{ route('billings.show', $b) }}">{{ $b->invoice_number }}</a>
                            @if($reminder)
                                <span class="badge badge-danger" title="Keterlambatan: {{ $reminder->days_overdue }} hari">
                                    <i class="fas fa-exclamation-circle"></i> {{ $reminder->days_overdue }}d
                                </span>
                            @endif
                        </td>
                        <td>{{ $b->consumer->nama ?? '-' }}</td>
                        <td>{{ $b->room->nomor_kamar ?? '-' }}</td>
                        <td>{{ $b->periode_awal->format('Y-m-d') }} - {{ $b->periode_akhir->format('Y-m-d') }}</td>
                        <td>Rp {{ number_format($b->total_tagihan,0,',','.') }}</td>
                        <td>
                            @if($b->status === 'lunas')
                                <span class="badge badge-success">{{ ucfirst($b->status) }}</span>
                            @elseif($b->status === 'sebagian')
                                <span class="badge badge-warning">{{ ucfirst($b->status) }}</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($b->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <div>Menampilkan {{ $billings->firstItem() }} - {{ $billings->lastItem() }} dari {{ $billings->total() }}</div>
                <div>{{ $billings->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection