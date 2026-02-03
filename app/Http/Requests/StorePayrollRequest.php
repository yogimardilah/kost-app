<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollRequest extends FormRequest
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
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('payrolls')->where(function ($query) {
                    return $query->where('bulan', $this->bulan)
                                 ->where('tahun', $this->tahun);
                }),
            ],
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'total_gaji' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required|in:pending,dibayar',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
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
            'employee_id' => 'Karyawan',
            'bulan' => 'Periode Bulan',
            'tahun' => 'Periode Tahun',
            'total_gaji' => 'Total Gaji',
            'bonus' => 'Bonus',
            'potongan' => 'Potongan',
            'tanggal_bayar' => 'Tanggal Bayar',
            'status' => 'Status',
            'keterangan' => 'Keterangan',
            'file' => 'File',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.unique' => 'Payroll untuk karyawan ini di periode yang sama sudah ada.',
        ];
    }
}
