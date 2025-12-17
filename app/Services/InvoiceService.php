<?php

namespace App\Services;

use App\Models\Billing;

class InvoiceService
{
    /**
     * Generate invoice data for display/PDF rendering.
     *
     * @param Billing $billing
     * @return array
     */
    public static function getInvoiceData(Billing $billing): array
    {
        $totalPaid = $billing->payments->sum('jumlah');
        $balance = $billing->total_tagihan - $totalPaid;

        return [
            'billing' => $billing,
            'consumer' => $billing->consumer,
            'room' => $billing->room,
            'kost' => $billing->room->kost,
            'details' => $billing->details,
            'payments' => $billing->payments,
            'totalTagihan' => $billing->total_tagihan,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
            'status' => $billing->status,
            'invoiceDate' => now()->format('Y-m-d'),
        ];
    }

    /**
     * Generate invoice HTML for PDF rendering.
     *
     * @param Billing $billing
     * @return string
     */
    public static function generateInvoiceHtml(Billing $billing): string
    {
        $data = static::getInvoiceData($billing);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {$billing->invoice_number}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info h1 {
            font-size: 24px;
            font-weight: bold;
        }
        .company-info p {
            margin: 5px 0;
            font-size: 11px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta p {
            margin: 5px 0;
            font-weight: bold;
        }
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .invoice-info-box {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .invoice-info-box h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .invoice-info-box p {
            margin: 3px 0;
            font-size: 11px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table thead {
            background-color: #f5f5f5;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }
        .items-table th, .items-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .items-table th {
            font-size: 12px;
            font-weight: bold;
        }
        .items-table td {
            font-size: 11px;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-top: 20px;
        }
        .totals-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .totals-box {
            border: 1px solid #ddd;
            padding: 15px;
        }
        .totals-box h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .totals-box .total-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 11px;
        }
        .totals-box .total-item.total {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending {
            background-color: #ffebee;
            color: #c62828;
        }
        .status-sebagian {
            background-color: #fff3e0;
            color: #e65100;
        }
        .status-lunas {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{$data['kost']->nama_kost ?? 'KOST'}</h1>
                <p>{$data['kost']->alamat ?? ''}</p>
                <p>Telp: {$data['kost']->telepon ?? '-'}</p>
            </div>
            <div class="invoice-meta">
                <p>INVOICE</p>
                <p style="font-size: 14px; color: #c62828;">{$billing->invoice_number}</p>
                <p>Tanggal: {$data['invoiceDate']}</p>
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-box">
                <h3>PENYEWA</h3>
                <p><strong>{$data['consumer']->nama ?? '-'}</strong></p>
                <p>No. HP: {$data['consumer']->no_hp ?? '-'}</p>
                <p>Email: {$data['consumer']->email ?? '-'}</p>
            </div>
            <div class="invoice-info-box">
                <h3>KAMAR</h3>
                <p><strong>Nomor Kamar: {$data['room']->nomor_kamar ?? '-'}</strong></p>
                <p>Jenis: {$data['room']->jenis_kamar ?? '-'}</p>
                <p>Periode: {$data['billing']->periode_awal->format('Y-m-d')} s/d {$data['billing']->periode_akhir->format('Y-m-d')}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Deskripsi</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
HTML;

        $no = 1;
        foreach ($data['details'] as $detail) {
            $html .= <<<HTML
                <tr>
                    <td>{$no}</td>
                    <td>{$detail->keterangan}</td>
                    <td class="text-right">{$detail->qty}</td>
                    <td class="text-right">Rp {number_format($detail->harga, 0, ',', '.')}</td>
                    <td class="text-right">Rp {number_format($detail->subtotal, 0, ',', '.')}</td>
                </tr>
HTML;
            $no++;
        }

        $html .= <<<HTML
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <div></div>
                <div class="totals-box">
                    <h3>RINGKASAN PEMBAYARAN</h3>
                    <div class="total-item">
                        <span>Total Tagihan:</span>
                        <span>Rp {number_format($data['totalTagihan'], 0, ',', '.')}</span>
                    </div>
                    <div class="total-item">
                        <span>Total Dibayar:</span>
                        <span>Rp {number_format($data['totalPaid'], 0, ',', '.')}</span>
                    </div>
                    <div class="total-item total">
                        <span>Sisa Tagihan:</span>
                        <span>Rp {number_format($data['balance'], 0, ',', '.')}</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <span class="status-badge status-{$data['status']}">{strtoupper($data['status'])}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menjadi penyewa kami. Invoice ini sah sebagai bukti transaksi.</p>
            <p>Dicetak pada: {now()->format('Y-m-d H:i:s')}</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
