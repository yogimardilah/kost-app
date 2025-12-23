<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomOccupancyRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'consumer_id' => 'required|exists:consumers,id',
            'tipe_sewa' => 'required|in:bulanan,harian',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'status' => 'required|in:aktif,selesai,batal',
        ];
    }

    public function messages()
    {
        return [
            'room_id.required' => 'Pilih kamar',
            'consumer_id.required' => 'Pilih konsumen',
            'tipe_sewa.required' => 'Pilih tipe sewa (bulanan/harian)',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'status.required' => 'Status harus diisi',
        ];
    }
}
