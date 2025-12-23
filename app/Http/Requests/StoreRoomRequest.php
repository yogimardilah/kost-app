<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'nomor_kamar' => 'required|string|unique:rooms,nomor_kamar',
            'jenis_kamar' => 'required|string|in:single,double,suite',
            'harga' => 'required|numeric|min:50000',
            'harga_harian' => 'nullable|numeric|min:5000',
            'status' => 'required|in:tersedia,terisi',
            'kost_id' => 'required|exists:kosts,id',
        ];
    }
}
