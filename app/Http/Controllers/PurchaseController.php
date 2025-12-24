<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('kost')->orderBy('purchase_date', 'desc');

        if ($request->filled('kost_id')) {
            $query->where('kost_id', $request->get('kost_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where('description', 'like', "%{$search}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->get('date_to'));
        }

        $purchases = $query->paginate(20)->withQueryString();
        $kosts = Kost::orderBy('nama_kost')->get();

        return view('purchases.index', compact('purchases', 'kosts'));
    }

    public function print(Request $request)
    {
        $query = Purchase::with('kost')->orderBy('purchase_date', 'desc');

        if ($request->filled('kost_id')) {
            $query->where('kost_id', $request->get('kost_id'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where('description', 'like', "%{$search}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->get('date_to'));
        }

        $purchases = $query->get();
        return view('purchases.print', compact('purchases'));
    }

    public function export(Request $request)
    {
        $query = Purchase::with('kost')->orderBy('purchase_date', 'desc');

        if ($request->filled('kost_id')) {
            $query->where('kost_id', $request->get('kost_id'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where('description', 'like', "%{$search}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->get('date_to'));
        }

        $purchases = $query->get();

        $filename = 'purchases_' . now()->format('Ymd_His') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($purchases) {
            // HTML table is the simplest Excel-compatible format with proper column alignment
            echo '\xEF\xBB\xBF'; // UTF-8 BOM
            echo '<html><head><meta charset="UTF-8"><style>table { border-collapse: collapse; } td, th { border:1px solid #000; padding:4px; }</style></head><body>';
            echo '<h3>Daftar Pembelian/Ops</h3>';
            echo '<table>'; 
            echo '<thead><tr>'
                . '<th>No</th>'
                . '<th>Tanggal</th>'
                . '<th>Kost</th>'
                . '<th>Deskripsi</th>'
                . '<th>Kategori</th>'
                . '<th>Jumlah</th>'
                . '<th>Catatan</th>'
                . '</tr></thead><tbody>';
            $idx = 1;
            foreach ($purchases as $p) {
                echo '<tr>'
                    . '<td>' . $idx++ . '</td>'
                    . '<td>' . e(optional($p->purchase_date)->format('Y-m-d')) . '</td>'
                    . '<td>' . e(optional($p->kost)->nama_kost) . '</td>'
                    . '<td>' . e($p->description) . '</td>'
                    . '<td>' . e(str_replace('_', ' ', $p->category)) . '</td>'
                    . '<td>' . e(number_format($p->amount, 0, ',', '.')) . '</td>'
                    . '<td>' . e($p->notes) . '</td>'
                    . '</tr>';
            }
            if ($purchases->isEmpty()) {
                echo '<tr><td colspan="7">Tidak ada data</td></tr>';
            }
            echo '</tbody></table></body></html>';
        }, $filename, $headers);
    }

    public function create()
    {
        $kosts = Kost::orderBy('nama_kost')->get();
        return view('purchases.create', compact('kosts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kost_id' => 'required|exists:kosts,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            // Accept based on extension OR common Office/PDF/Image MIME types (handles octet-stream uploads too)
            'file' => [
                'nullable',
                'file',
                'max:5120',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $ext = strtolower($value->getClientOriginalExtension() ?? '');
                    $mime = strtolower($value->getMimeType() ?? '');
                    $allowedExts = ['pdf','jpg','jpeg','png','doc','docx','xlsx','xls'];
                    $allowedMimes = [
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/msexcel',
                        'application/octet-stream', // allow when extension is valid
                    ];
                    $extOk = in_array($ext, $allowedExts, true);
                    $mimeOk = in_array($mime, $allowedMimes, true);
                    // Accept if extension is allowed OR mime is allowed
                    if (!($extOk || $mimeOk)) {
                        $fail('File harus bertipe: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX.');
                    }
                },
            ],
        ]);

        $purchase = new Purchase();
        $purchase->kost_id = $data['kost_id'];
        $purchase->description = $data['description'];
        $purchase->category = $data['category'];
        $purchase->amount = $data['amount'];
        $purchase->purchase_date = $data['purchase_date'];
        $purchase->notes = $data['notes'] ?? null;
        $purchase->created_by = Auth::id();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueName = Str::uuid() . '.' . $extension;
            $path = $file->storeAs('purchases', $uniqueName, 'public');
            $purchase->file_path = $path;
        }

        $purchase->save();

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil ditambahkan');
    }

    public function edit(Purchase $purchase)
    {
        $kosts = Kost::orderBy('nama_kost')->get();
        return view('purchases.edit', compact('purchase', 'kosts'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'kost_id' => 'required|exists:kosts,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'file' => [
                'nullable',
                'file',
                'max:5120',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $ext = strtolower($value->getClientOriginalExtension() ?? '');
                    $mime = strtolower($value->getMimeType() ?? '');
                    $allowedExts = ['pdf','jpg','jpeg','png','doc','docx','xlsx','xls'];
                    $allowedMimes = [
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/msexcel',
                        'application/octet-stream',
                    ];
                    $extOk = in_array($ext, $allowedExts, true);
                    $mimeOk = in_array($mime, $allowedMimes, true);
                    if (!($extOk || $mimeOk)) {
                        $fail('File harus bertipe: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX.');
                    }
                },
            ],
        ]);

        $purchase->kost_id = $data['kost_id'];
        $purchase->description = $data['description'];
        $purchase->category = $data['category'];
        $purchase->amount = $data['amount'];
        $purchase->purchase_date = $data['purchase_date'];
        $purchase->notes = $data['notes'] ?? null;
        $purchase->updated_by = Auth::id();

        if ($request->hasFile('file')) {
            // Delete old file
            if ($purchase->file_path && Storage::disk('public')->exists($purchase->file_path)) {
                Storage::disk('public')->delete($purchase->file_path);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueName = Str::uuid() . '.' . $extension;
            $path = $file->storeAs('purchases', $uniqueName, 'public');
            $purchase->file_path = $path;
        }

        $purchase->save();

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diperbarui');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->deleted_by = Auth::id();
        $purchase->save();
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dihapus');
    }
}
