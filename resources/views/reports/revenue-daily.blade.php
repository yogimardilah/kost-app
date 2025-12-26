@extends('layouts.app')

@section('title', 'Chart Pendapatan Harian')

@section('content_header')
    <h1>Chart Pendapatan Harian</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pendapatan Harian</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('reports.revenue-daily') }}" class="form-inline">
                    <div class="input-group input-group-sm">
                        <input type="month" name="month" class="form-control" value="{{ $month }}" style="max-width: 150px;">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" style="max-height: 400px;"></canvas>
            
            <div class="mt-4">
                <h5>Total Pendapatan Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }}:</h5>
                <h3 class="text-success">Rp {{ number_format(array_sum($chartData['data']), 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($chartData['data']),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>
@endpush
