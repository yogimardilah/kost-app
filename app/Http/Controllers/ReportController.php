<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Billing;

class ReportController extends Controller
{
    public function occupancy()
    {
        $occupancies = RoomOccupancy::with('room','consumer')->orderBy('tanggal_masuk','desc')->get();
        return view('reports.occupancy', compact('occupancies'));
    }

    public function finance()
    {
        $billings = Billing::with('room','consumer')->orderBy('periode_awal','desc')->get();
        return view('reports.finance', compact('billings'));
    }
}
