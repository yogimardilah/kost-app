@extends('layouts.app')

@section('title', 'Cetak Pembelian/Ops')

@section('content_header')
    <h1 class="d-print-none">Cetak Pembelian/Ops</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kost</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($purchases as $idx => $purchase)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ optional($purchase->purchase_date)->format('d/m/Y') }}</td>
                            <td>{{ $purchase->kost->nama_kost ?? '-' }}</td>
                            <td>{{ $purchase->description }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $purchase->category)) }}</td>
                            <td>Rp {{ number_format($purchase->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
@media print {
    .main-header, .main-sidebar, .content-header, .main-footer, .btn, .card-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
}
</style>
@endpush

@push('js')
<script>
    window.addEventListener('load', function () {
        setTimeout(function(){ window.print(); }, 300);
    });
</script>
@endpush
