<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->employee->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #34495e;
        }
        .info-value {
            display: table-cell;
        }
        .divider {
            border-top: 1px solid #bdc3c7;
            margin: 20px 0;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .salary-table th,
        .salary-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        .salary-table th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
        }
        .salary-table .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
            font-size: 14px;
        }
        .salary-table .total-row td {
            border-top: 2px solid #34495e;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #27ae60;
        }
        .text-danger {
            color: #e74c3c;
        }
        .text-primary {
            color: #3498db;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #bdc3c7;
            padding-top: 15px;
        }
        .signature {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            width: 200px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLIP GAJI KARYAWAN</h1>
        <p>Sistem Manajemen Kost</p>
        <p>{{ config('app.name', 'Kost Management') }}</p>
    </div>

    <div class="info-section">
        <h3 style="margin-bottom: 15px; color: #2c3e50;">Informasi Karyawan</h3>
        <div class="info-row">
            <span class="info-label">Nama</span>
            <span class="info-value">: {{ $payroll->employee->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">NIK</span>
            <span class="info-value">: {{ $payroll->employee->nik }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jabatan</span>
            <span class="info-value">: {{ $payroll->employee->jabatan }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">No. HP</span>
            <span class="info-value">: {{ $payroll->employee->no_hp }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-section">
        <h3 style="margin-bottom: 15px; color: #2c3e50;">Detail Penggajian</h3>
        <div class="info-row">
            <span class="info-label">No. Slip Gaji</span>
            <span class="info-value">: <strong style="color: #3498db;">{{ $payroll->slip_number }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">: <strong>{{ $payroll->periode }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value">: 
                @if($payroll->status == 'dibayar')
                    <span class="badge badge-success">DIBAYAR</span>
                @else
                    <span class="badge badge-warning">PENDING</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Bayar</span>
            <span class="info-value">: {{ $payroll->tanggal_bayar ? $payroll->tanggal_bayar->format('d F Y H:i:s') : '-' }}</span>
        </div>
    </div>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            @if($payroll->bonus > 0)
            <tr>
                <td class="text-success">Bonus</td>
                <td class="text-right text-success">+ {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->potongan > 0)
            <tr>
                <td class="text-danger">Potongan</td>
                <td class="text-right text-danger">- {{ number_format($payroll->potongan, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL GAJI BERSIH</td>
                <td class="text-right text-primary">{{ number_format($payroll->total_gaji, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($payroll->keterangan)
    <div class="info-section" style="margin-top: 20px;">
        <h4 style="margin-bottom: 10px; color: #2c3e50;">Keterangan:</h4>
        <p style="padding: 10px; background-color: #ecf0f1; border-radius: 5px;">
            {{ $payroll->keterangan }}
        </p>
    </div>
    @endif

    <div class="signature">
        <div class="signature-block">
            <p>Mengetahui,<br>Penerima</p>
            <div class="signature-line">
                {{ $payroll->employee->nama }}
            </div>
        </div>
        <div class="signature-block">
            <p>Menyetujui,<br>Pimpinan</p>
            <div class="signature-line">
                (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Slip gaji ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Dokumen ini sah dan diproses oleh sistem secara otomatis</p>
    </div>
</body>
</html>
