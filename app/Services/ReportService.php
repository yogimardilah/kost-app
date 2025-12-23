<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class ReportService
{
    /**
     * Generate occupancy report as CSV/XLSX format
     */
    public static function exportOccupancyExcel($occupancies)
    {
        // Gunakan CSV dengan delimiter semicolon agar lebih rapi dibuka di Excel (regional comma decimal)
        $filename = 'laporan-hunian-' . now()->format('Y-m-d') . '.csv';

        $delimiter = ';';

        // Generate CSV content
        $csv = '';

        // BOM for UTF-8 to handle Indonesian characters
        $csv .= "\xEF\xBB\xBF";

        // Kop Report
        $csv .= 'LAPORAN HUNIAN KAMAR KOST' . "\n";
        $csv .= 'Periode: ' . now()->format('d M Y') . "\n\n";

        // Headers
        $headers = [
            'No',
            'Nomor Kamar',
            'Penyewa',
            'NIK',
            'Check-in',
            'Check-out',
            'Status'
        ];
        $csv .= implode($delimiter, $headers) . "\n";

        // Data rows
        foreach ($occupancies as $i => $o) {
            $nik = $o->consumer->nik ?? '-';
            // Paksa dibaca sebagai teks (hindari notasi ilmiah / pemangkasan nol)
            $nikExcel = "\t" . $nik;

            $checkIn = $o->tanggal_masuk ? Carbon::parse($o->tanggal_masuk)->format('d/m/Y') : '-';
            $checkOut = $o->tanggal_keluar ? Carbon::parse($o->tanggal_keluar)->format('d/m/Y') : '-';

            $row = [
                $i + 1,
                ($o->room->nomor_kamar ?? '-'),
                ($o->consumer->nama ?? '-'),
                $nikExcel,
                $checkIn,
                $checkOut,
                ucfirst($o->status)
            ];

            $csv .= implode($delimiter, $row) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename={$filename}")
            ->header('Cache-Control', 'max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
