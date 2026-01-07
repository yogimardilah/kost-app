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
        // Get income from billings
        $billingQuery = Billing::with(['room','consumer','payments'])
            ->selectRaw("'billing' as type, id, invoice_number as reference, created_at as transaction_date, total_tagihan as amount, status, consumer_id, room_id, NULL as description, periode_awal, periode_akhir");

        // Search by invoice, room number, consumer name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $billingQuery->where(function($q) use ($search) {
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

        // Date range by invoice date (created_at)
        if ($request->filled('start_date')) {
            $billingQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billingQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // Status filter (pending, sebagian, lunas)
        if ($request->filled('status')) {
            $billingQuery->where('status', $request->status);
        }

        // Get expenses from purchases
        $purchaseQuery = \App\Models\Purchase::query()
            ->selectRaw("'purchase' as type, id, description as reference, purchase_date as transaction_date, amount, 'expense' as status, NULL as consumer_id, NULL as room_id, category as description, NULL as periode_awal, NULL as periode_akhir");

        if ($request->filled('search')) {
            $search = $request->search;
            $purchaseQuery->where('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('start_date')) {
            $purchaseQuery->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $purchaseQuery->whereDate('purchase_date', '<=', $request->end_date);
        }

        // Get expenses from payrolls
        $payrollQuery = \App\Models\Payroll::with('employee')
            ->selectRaw("'payroll' as type, id, CONCAT('Gaji ', (SELECT nama FROM employees WHERE id = payrolls.employee_id)) as reference, COALESCE(tanggal_bayar, created_at) as transaction_date, total_gaji as amount, 'expense' as status, NULL as consumer_id, NULL as room_id, CONCAT(bulan, '/', tahun) as description, NULL as periode_awal, NULL as periode_akhir");

        if ($request->filled('search')) {
            $search = $request->search;
            $payrollQuery->whereHas('employee', function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $payrollQuery->where(function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '>=', $request->start_date)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('tanggal_bayar')
                          ->whereDate('created_at', '>=', $request->start_date);
                  });
            });
        }
        if ($request->filled('end_date')) {
            $payrollQuery->where(function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '<=', $request->end_date)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('tanggal_bayar')
                          ->whereDate('created_at', '<=', $request->end_date);
                  });
            });
        }

        // Only show paid payrolls
        $payrollQuery->where('status', 'dibayar');

        // Combine all queries
        $query = $billingQuery->union($purchaseQuery)->union($payrollQuery);

        // Export to Excel
        if ($request->has('export') && $request->export === 'excel') {
            $allData = \DB::table(\DB::raw("(({$billingQuery->toSql()}) UNION ({$purchaseQuery->toSql()}) UNION ({$payrollQuery->toSql()})) as combined"))
                ->mergeBindings($billingQuery->getQuery())
                ->mergeBindings($purchaseQuery->getQuery())
                ->mergeBindings($payrollQuery->getQuery())
                ->orderBy('transaction_date', 'desc')
                ->get();
            return $this->exportFinanceExcel($allData);
        }

        // Calculate totals
        $allBillings = Billing::query();
        $allPurchases = \App\Models\Purchase::query();
        $allPayrolls = \App\Models\Payroll::where('status', 'dibayar');
        
        if ($request->filled('start_date')) {
            $allBillings->whereDate('created_at', '>=', $request->start_date);
            $allPurchases->whereDate('purchase_date', '>=', $request->start_date);
            $allPayrolls->where(function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '>=', $request->start_date)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('tanggal_bayar')
                          ->whereDate('created_at', '>=', $request->start_date);
                  });
            });
        }
        if ($request->filled('end_date')) {
            $allBillings->whereDate('created_at', '<=', $request->end_date);
            $allPurchases->whereDate('purchase_date', '<=', $request->end_date);
            $allPayrolls->where(function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '<=', $request->end_date)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('tanggal_bayar')
                          ->whereDate('created_at', '<=', $request->end_date);
                  });
            });
        }
        if ($request->filled('status')) {
            $allBillings->where('status', $request->status);
            $allPayrolls->whereHas('employee', function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $totalBilled = $allBillings->sum('total_tagihan');
        $totalPaid = \DB::table('payments')
            ->whereIn('billing_id', $allBillings->pluck('id'))
            ->sum('jumlah');
        $totalExpenses = $allPurchases->sum('amount');
        $totalPayroll = $allPayrolls->sum('total_gaji');
        $outstanding = $totalBilled - $totalPaid;
        $netIncome = $totalPaid - $totalExpenses - $totalPayroll;

        // Paginate combined results
        $transactions = \DB::table(\DB::raw("(({$billingQuery->toSql()}) UNION ({$purchaseQuery->toSql()}) UNION ({$payrollQuery->toSql()})) as combined"))
            ->mergeBindings($billingQuery->getQuery())
            ->mergeBindings($purchaseQuery->getQuery())
            ->mergeBindings($payrollQuery->getQuery())
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('reports.finance', compact('transactions', 'totalBilled', 'totalPaid', 'totalExpenses', 'totalPayroll', 'outstanding', 'netIncome'));
    }

    private function exportFinanceExcel($transactions)
    {
        $filename = 'laporan-keuangan-' . date('Y-m-d') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($transactions) {
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
            echo '<th>Tanggal</th>';
            echo '<th>Tipe</th>';
            echo '<th>Referensi</th>';
            echo '<th>Keterangan</th>';
            echo '<th>Pendapatan</th>';
            echo '<th>Pengeluaran</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($transactions as $i => $t) {
                $date = Carbon::parse($t->transaction_date)->format('d/m/Y');
                $tipe = $t->type === 'billing' ? 'Tagihan' : 'Operasional';
                $pendapatan = $t->type === 'billing' ? (int)$t->amount : 0;
                $pengeluaran = $t->type === 'purchase' ? (int)$t->amount : 0;
                $keterangan = $t->type === 'billing' ? ($t->periode_awal && $t->periode_akhir ? Carbon::parse($t->periode_awal)->format('d/m/Y') . ' - ' . Carbon::parse($t->periode_akhir)->format('d/m/Y') : '-') : ($t->description ?? '-');

                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . $date . '</td>';
                echo '<td>' . $tipe . '</td>';
                echo '<td>' . ($t->reference ?? '-') . '</td>';
                echo '<td>' . $keterangan . '</td>';
                echo '<td>' . $pendapatan . '</td>';
                echo '<td>' . $pengeluaran . '</td>';
                echo '<td>' . ucfirst($t->status ?? '-') . '</td>';
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
