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

        // Prepare simple variables to avoid complex expressions inside heredoc interpolation
        $kost = $data['kost'];
        $kostName = $kost->nama_kost ?? 'KOST';
        $kostAlamat = $kost->alamat ?? '';
        $kostTelp = $kost->telepon ?? '-';

        $consumer = $data['consumer'];
        $consumerNama = $consumer->nama ?? '-';
        $consumerHp = $consumer->no_hp ?? '-';
        $consumerEmail = $consumer->email ?? '-';

        $room = $data['room'];
        $roomNomor = $room->nomor_kamar ?? '-';
        $roomJenis = $room->jenis_kamar ?? '-';

        $periodStart = $data['billing']->periode_awal->format('Y-m-d');
        $periodEnd = $data['billing']->periode_akhir->format('Y-m-d');
        $invoiceNumber = $billing->invoice_number;
        $invoiceDate = $data['invoiceDate'];
        $details = $data['details'];
        $totalTagihan = number_format($data['totalTagihan'], 0, ',', '.');
        $totalPaid = number_format($data['totalPaid'], 0, ',', '.');
        $balance = number_format($data['balance'], 0, ',', '.');
        $status = $data['status'];
        $statusUpper = strtoupper($status);
        $printedAt = now()->format('Y-m-d H:i:s');

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
            line-height: 1.4;
        }
        .container {
            padding: 0 10px;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding: 10px 0 15px 0;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        .company-info h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        .company-info p {
            margin: 3px 0;
            font-size: 10px;
        }
        .invoice-meta {
            text-align: right;
            page-break-after: avoid;
        }
        .invoice-meta p {
            margin: 3px 0;
            font-weight: bold;
            font-size: 11px;
        }
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            page-break-after: avoid;
        }
        .invoice-info-box {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .invoice-info-box h3 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .invoice-info-box p {
            margin: 2px 0;
            font-size: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-after: avoid;
        }
        .items-table thead {
            background-color: #f5f5f5;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }
        .items-table th, .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 10px;
        }
        .items-table th {
            font-size: 11px;
            font-weight: bold;
        }
        .items-table td {
            font-size: 10px;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .totals-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }
        .totals-box {
            border: 1px solid #ddd;
            padding: 12px;
        }
        .totals-box h3 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .totals-box .total-item {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 10px;
        }
        .totals-box .total-item.total {
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-top: 5px;
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
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{$kostName}</h1>
                <p>{$kostAlamat}</p>
                <p>Telp: {$kostTelp}</p>
            </div>
            <div class="invoice-meta">
                <p>INVOICE</p>
                <p style="font-size: 14px; color: #c62828;">{$invoiceNumber}</p>
                <p>Tanggal: {$invoiceDate}</p>
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-box">
                <h3>PENYEWA</h3>
                <p><strong>{$consumerNama}</strong></p>
                <p>No. HP: {$consumerHp}</p>
                <p>Email: {$consumerEmail}</p>
            </div>
            <div class="invoice-info-box">
                <h3>KAMAR</h3>
                <p><strong>Nomor Kamar: {$roomNomor}</strong></p>
                <p>Jenis: {$roomJenis}</p>
                <p>Periode: {$periodStart} s/d {$periodEnd}</p>
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
        foreach ($details as $detail) {
            $html .= "                <tr>\n";
            $html .= "                    <td>{$no}</td>\n";
            $html .= "                    <td>{$detail->keterangan}</td>\n";
            $html .= "                    <td class=\"text-right\">{$detail->qty}</td>\n";
            $html .= "                    <td class=\"text-right\">Rp " . number_format($detail->harga, 0, ',', '.') . "</td>\n";
            $html .= "                    <td class=\"text-right\">Rp " . number_format($detail->subtotal, 0, ',', '.') . "</td>\n";
            $html .= "                </tr>\n";
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
                        <span>Rp {$totalTagihan}</span>
                    </div>
                    <div class="total-item">
                        <span>Total Dibayar:</span>
                        <span>Rp {$totalPaid}</span>
                    </div>
                    <div class="total-item total">
                        <span>Sisa Tagihan:</span>
                        <span>Rp {$balance}</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <span class="status-badge status-{$status}">{$statusUpper}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menjadi penyewa kami. Invoice ini sah sebagai bukti transaksi.</p>
            <p>Dicetak pada: {$printedAt}</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
