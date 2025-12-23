<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumerRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $consumerId = $this->route('consumer') ? $this->route('consumer')->id : null;

        return [
            'nik' => 'required|string|max:20|unique:consumers,nik,' . $consumerId,
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'kendaraan' => 'nullable|string|max:255',
            'tanda_pengenal' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'nik.required' => 'NIK harus diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama harus diisi',
            'no_hp.required' => 'Nomor HP harus diisi',
        ];
    }
}
