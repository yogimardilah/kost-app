<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Http\Request;
use PDF;
use Intervention\Image\Facades\Image;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payroll::with('employee');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $payrolls = $query->orderBy('tahun', 'desc')
                          ->orderBy('bulan', 'desc')
                          ->paginate(20)
                          ->withQueryString();

        // Get data for filters
        $employees = Employee::where('status', 'aktif')->orderBy('nama')->get();
        $years = Payroll::selectRaw('DISTINCT tahun')->orderBy('tahun', 'desc')->pluck('tahun');

        // Calculate summary
        $totalPending = Payroll::where('status', 'pending')->sum('total_gaji');
        $totalDibayar = Payroll::where('status', 'dibayar')->sum('total_gaji');

        return view('payrolls.index', compact('payrolls', 'employees', 'years', 'totalPending', 'totalDibayar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 'aktif')->orderBy('nama')->get();
        return view('payrolls.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayrollRequest $request)
    {
        $data = $request->validated();

        // Defensive fallback to prevent null gaji_pokok in DB.
        if (!array_key_exists('gaji_pokok', $data) || $data['gaji_pokok'] === null || $data['gaji_pokok'] === '') {
            $employee = Employee::find($data['employee_id']);
            $data['gaji_pokok'] = $employee?->gaji ?? 0;
        }
        
        // Generate slip number: SLIP/YYYY/MM/XXX
        $year = $data['tahun'];
        $month = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);
        $lastSlip = Payroll::where('tahun', $year)
            ->where('bulan', $data['bulan'])
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastSlip ? (intval(substr($lastSlip->slip_number, -3)) + 1) : 1;
        $data['slip_number'] = sprintf('SLIP/%s/%s/%03d', $year, $month, $sequence);
        
        // total_gaji comes from user input (manual)
        
        // Handle file upload with compression for images
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalName);
            $filename = time() . '_' . uniqid() . '_' . $sanitizedName . '.' . $extension;
            
            // Compress if it's an image
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $image = Image::make($file);
                
                // Resize if too large (max width 1920px)
                if ($image->width() > 1920) {
                    $image->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                // Create directory if it doesn't exist
                $directory = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'payrolls');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Compress and save
                $path = 'payrolls/' . $filename;
                $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
                $image->save($fullPath, 75);
                $data['file_path'] = $path;
            } else {
                // Non-image files, store normally
                $data['file_path'] = $file->storeAs('payrolls', $filename, 'public');
            }
        }
        
        Payroll::create($data);

        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        $payroll->load('employee');
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'aktif')->orderBy('nama')->get();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePayrollRequest $request, Payroll $payroll)
    {
        $data = $request->validated();

        // Defensive fallback to prevent null gaji_pokok in DB.
        if (!array_key_exists('gaji_pokok', $data) || $data['gaji_pokok'] === null || $data['gaji_pokok'] === '') {
            $employee = Employee::find($data['employee_id']);
            $data['gaji_pokok'] = $employee?->gaji ?? 0;
        }
        
        // total_gaji comes from user input (manual)
        
        // Handle file upload with compression for images
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($payroll->file_path && \Storage::disk('public')->exists($payroll->file_path)) {
                \Storage::disk('public')->delete($payroll->file_path);
            }
            
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalName);
            $filename = time() . '_' . uniqid() . '_' . $sanitizedName . '.' . $extension;
            
            // Compress if it's an image
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $image = Image::make($file);
                
                // Resize if too large (max width 1920px)
                if ($image->width() > 1920) {
                    $image->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                // Create directory if it doesn't exist
                $directory = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'payrolls');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Compress and save
                $path = 'payrolls/' . $filename;
                $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
                $image->save($fullPath, 75);
                $data['file_path'] = $path;
            } else {
                // Non-image files, store normally
                $data['file_path'] = $file->storeAs('payrolls', $filename, 'public');
            }
        }
        
        $payroll->update($data);

        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil dihapus');
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(Request $request, Payroll $payroll)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        $payroll->update([
            'status' => 'dibayar',
            'tanggal_bayar' => $request->tanggal_bayar,
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil ditandai sebagai dibayar');
    }

    /**
     * Print payroll slip as PDF
     */
    public function printSlip(Payroll $payroll)
    {
        $payroll->load('employee');
        
        $html = view('payrolls.slip', compact('payroll'))->render();
        
        try {
            $pdf = PDF::loadHTML($html)
                ->setPaper('a4')
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10);

            return $pdf->download('Slip-Gaji-' . $payroll->employee->nama . '-' . $payroll->periode . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('payrolls.show', $payroll)
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
