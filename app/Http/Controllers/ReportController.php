<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Billing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function occupancy(Request $request)
    {
        $query = RoomOccupancy::with('room','consumer');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('room', function($sub) use ($search) {
                    $sub->where('nomor_kamar', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('consumer', function($sub) use ($search) {
                    $sub->where('nama', 'LIKE', "%{$search}%")
                        ->orWhere('nik', 'LIKE', "%{$search}%");
                });
            });
        }
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_masuk', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_masuk', '<=', $request->end_date);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $query->orderBy('tanggal_masuk','desc');
        
        // Export to Excel
        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportOccupancyExcel($query->get());
        }
        
        $occupancies = $query->paginate(20)->withQueryString();
        
        return view('reports.occupancy', compact('occupancies'));
    }
    
    private function exportOccupancyExcel($occupancies)
    {
        $filename = 'laporan-hunian-' . date('Y-m-d') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($occupancies) {
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            echo '<x:Name>Laporan Hunian</x:Name>';
            echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
            echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '</head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
            echo '<th>No</th>';
            echo '<th>Nomor Kamar</th>';
            echo '<th>Penyewa</th>';
            echo '<th>NIK</th>';
            echo '<th>Check-in</th>';
            echo '<th>Check-out</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($occupancies as $i => $o) {
                $nik = $o->consumer->nik ?? '-';
                $checkIn = $o->tanggal_masuk ? \Carbon\Carbon::parse($o->tanggal_masuk)->format('d/m/Y') : '-';
                $checkOut = $o->tanggal_keluar ? \Carbon\Carbon::parse($o->tanggal_keluar)->format('d/m/Y') : '-';
                
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . ($o->room->nomor_kamar ?? '-') . '</td>';
                echo '<td>' . ($o->consumer->nama ?? '-') . '</td>';
                echo '<td style="mso-number-format:\'\@\';">' . $nik . '</td>'; // Format as text to preserve leading zeros
                echo '<td>' . $checkIn . '</td>';
                echo '<td>' . $checkOut . '</td>';
                echo '<td>' . ucfirst($o->status) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        };
        
        return response()->stream($callback, 200, $headers);
    }

    

    /**
     * Finance report with search, filters, pagination and export.
     */
    public function finance(Request $request)
    {
        $query = Billing::with(['room','consumer','payments']);

        // Search by invoice, room number, consumer name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('room', function($r) use ($search) {
                      $r->where('nomor_kamar', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('consumer', function($c) use ($search) {
                      $c->where('nama', 'LIKE', "%{$search}%")
                        ->orWhere('nik', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date range by periode_awal and periode_akhir
        if ($request->filled('start_date')) {
            $query->whereDate('periode_awal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('periode_akhir', '<=', $request->end_date);
        }

        // Status filter (pending, sebagian, lunas)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('periode_awal','desc');

        // Export to Excel
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportFinanceExcel($query->get());
        }

        // Totals across filtered dataset
        $allForTotals = (clone $query)->get();
        $totalBilled = $allForTotals->sum('total_tagihan');
        $totalPaid = $allForTotals->sum(function($b) { return $b->payments->sum('jumlah'); });
        $outstanding = $totalBilled - $totalPaid;

        // Paginate for table
        $billings = $query->paginate(20)->withQueryString();

        return view('reports.finance', compact('billings', 'totalBilled', 'totalPaid', 'outstanding'));
    }

    private function exportFinanceExcel($billings)
    {
        $filename = 'laporan-keuangan-' . date('Y-m-d') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($billings) {
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            echo '<x:Name>Laporan Keuangan</x:Name>';
            echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
            echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '</head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
            echo '<th>No</th>';
            echo '<th>Invoice</th>';
            echo '<th>Penyewa</th>';
            echo '<th>NIK</th>';
            echo '<th>Kamar</th>';
            echo '<th>Periode Awal</th>';
            echo '<th>Periode Akhir</th>';
            echo '<th>Total Tagihan</th>';
            echo '<th>Total Pembayaran</th>';
            echo '<th>Sisa</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($billings as $i => $b) {
                $paid = $b->payments->sum('jumlah');
                $left = ($b->total_tagihan ?? 0) - $paid;
                $nik = $b->consumer->nik ?? '-';
                $periodeAwal = $b->periode_awal ? Carbon::parse($b->periode_awal)->format('d/m/Y') : '-';
                $periodeAkhir = $b->periode_akhir ? Carbon::parse($b->periode_akhir)->format('d/m/Y') : '-';

                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . ($b->invoice_number ?? '-') . '</td>';
                echo '<td>' . ($b->consumer->nama ?? '-') . '</td>';
                echo '<td style="mso-number-format:\'\@\';">' . $nik . '</td>';
                echo '<td>' . ($b->room->nomor_kamar ?? '-') . '</td>';
                echo '<td>' . $periodeAwal . '</td>';
                echo '<td>' . $periodeAkhir . '</td>';
                echo '<td>' . (is_null($b->total_tagihan) ? 0 : (int)$b->total_tagihan) . '</td>';
                echo '<td>' . (int)$paid . '</td>';
                echo '<td>' . (int)$left . '</td>';
                echo '<td>' . ucfirst($b->status ?? '-') . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
