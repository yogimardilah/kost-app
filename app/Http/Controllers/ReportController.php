<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Billing;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;

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
            return ReportService::exportOccupancyExcel($query->get());
        }

        $occupancies = $query->paginate(20)->withQueryString();
        
        return view('reports.occupancy', compact('occupancies'));
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

    /**
     * Chart Pendapatan Harian
     */
    public function revenueDaily(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Get daily payments for the selected month
        $payments = Payment::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(tanggal_bayar) as date'),
                DB::raw('SUM(jumlah) as total')
            )
            ->groupBy(DB::raw('DATE(tanggal_bayar)'))
            ->orderBy('date')
            ->get();

        // Create array of all days in month
        $days = [];
        $revenues = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $days[] = $current->format('d M');
            
            $payment = $payments->firstWhere('date', $dateStr);
            $revenues[] = $payment ? (float)$payment->total : 0;
            
            $current->addDay();
        }

        $chartData = [
            'labels' => $days,
            'data' => $revenues,
        ];

        return view('reports.revenue-daily', compact('chartData', 'month'));
    }

    /**
     * Chart Pendapatan Bulanan
     */
    public function revenueMonthly(Request $request)
    {
        $year = $request->get('year', now()->year);

        // Get monthly payments for the selected year
        $payments = Payment::whereYear('tanggal_bayar', $year)
            ->select(
                DB::raw('EXTRACT(MONTH FROM tanggal_bayar) as month'),
                DB::raw('SUM(jumlah) as total')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal_bayar)'))
            ->orderBy('month')
            ->get();

        // Create array for all 12 months
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $revenues = [];

        for ($i = 1; $i <= 12; $i++) {
            $payment = $payments->firstWhere('month', $i);
            $revenues[] = $payment ? (float)$payment->total : 0;
        }

        $chartData = [
            'labels' => $months,
            'data' => $revenues,
        ];

        return view('reports.revenue-monthly', compact('chartData', 'year'));
    }

    /**
     * Chart Traffic In/Out Harian (per Bulan)
     */
    public function traffic(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Get daily check-ins
        $checkIns = RoomOccupancy::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(tanggal_masuk) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DATE(tanggal_masuk)'))
            ->orderBy('date')
            ->get();

        // Get daily check-outs
        $checkOuts = RoomOccupancy::whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where('status', 'tidak aktif')
            ->select(
                DB::raw('DATE(tanggal_keluar) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DATE(tanggal_keluar)'))
            ->orderBy('date')
            ->get();

        // Create array of all days in month
        $days = [];
        $ins = [];
        $outs = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $days[] = $current->format('d M');
            
            $checkIn = $checkIns->firstWhere('date', $dateStr);
            $ins[] = $checkIn ? (int)$checkIn->total : 0;
            
            $checkOut = $checkOuts->firstWhere('date', $dateStr);
            $outs[] = $checkOut ? (int)$checkOut->total : 0;
            
            $current->addDay();
        }

        $chartData = [
            'labels' => $days,
            'checkIns' => $ins,
            'checkOuts' => $outs,
        ];

        return view('reports.traffic', compact('chartData', 'month'));
    }
}
