<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomOccupancyRequest extends FormRequest
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
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
        ];
    }

    public function messages()
    {
        return [
            'room_id.required' => 'Pilih kamar',
            'consumer_id.required' => 'Pilih konsumen',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
        ];
    }
}
