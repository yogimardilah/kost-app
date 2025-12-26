@extends('layouts.app')

@section('title', 'Chart Traffic In/Out')

@section('content_header')
    <h1>Chart Traffic In/Out</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Traffic Check-In & Check-Out Harian</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('reports.traffic') }}" class="form-inline">
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
            <canvas id="trafficChart" style="max-height: 400px;"></canvas>
            
            <div class="mt-4 row">
                <div class="col-md-6">
                    <h5>Total Check-In Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }}:</h5>
                    <h3 class="text-success">{{ array_sum($chartData['checkIns']) }} Penyewa</h3>
                </div>
                <div class="col-md-6">
                    <h5>Total Check-Out Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }}:</h5>
                    <h3 class="text-danger">{{ array_sum($chartData['checkOuts']) }} Penyewa</h3>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Check-In',
                    data: @json($chartData['checkIns']),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Check-Out',
                    data: @json($chartData['checkOuts']),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' orang';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' orang';
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
