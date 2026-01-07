<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:20', Rule::unique('employees', 'nik')->ignore($this->employee)],
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_bergabung' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_bergabung',
            'gaji' => 'required|numeric|min:0',
            'tanggal_gajian' => 'required|integer|min:1|max:31',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:aktif,tidak aktif',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nik' => 'NIK',
            'nama' => 'Nama Lengkap',
            'jabatan' => 'Jabatan',
            'tanggal_bergabung' => 'Tanggal Bergabung',
            'tanggal_berakhir' => 'Tanggal Berakhir',
            'gaji' => 'Gaji',
            'tanggal_gajian' => 'Tanggal Gajian',
            'no_hp' => 'No. HP',
            'alamat' => 'Alamat',
            'foto' => 'Foto',
            'status' => 'Status',
        ];
    }
}
