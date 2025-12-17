<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $roomId = $this->route('room') ? $this->route('room')->id : null;

        return [
            'nomor_kamar' => 'required|string|unique:rooms,nomor_kamar,' . $roomId,
            'jenis_kamar' => 'required|string|in:single,double,suite',
            'harga' => 'required|numeric|min:50000',
            'status' => 'required|in:tersedia,terisi',
            'kost_id' => 'required|exists:kosts,id',
        ];
    }
}
