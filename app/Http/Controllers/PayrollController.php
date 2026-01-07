<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Http\Request;

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
        
        // Calculate total gaji
        $data['total_gaji'] = $data['gaji_pokok'] + $data['bonus'] - $data['potongan'];
        
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
        
        // Calculate total gaji
        $data['total_gaji'] = $data['gaji_pokok'] + $data['bonus'] - $data['potongan'];
        
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
}
