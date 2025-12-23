<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'billing_id' => 'required|exists:billings,id',
            'tanggal_bayar' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'metode' => 'required|in:tunai,transfer,cek',
            'bukti_bayar' => 'nullable|string',
            'bukti_bayar_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'billing_id.required' => 'Billing harus dipilih',
            'billing_id.exists' => 'Billing tidak ditemukan',
            'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi',
            'tanggal_bayar.date' => 'Format tanggal pembayaran tidak valid',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.numeric' => 'Jumlah pembayaran harus berupa angka',
            'jumlah.min' => 'Jumlah pembayaran harus lebih dari 0',
            'metode.required' => 'Metode pembayaran wajib dipilih',
            'metode.in' => 'Metode pembayaran harus salah satu dari: tunai, transfer, cek',
            'bukti_bayar_file.file' => 'Bukti pembayaran harus berupa file',
            'bukti_bayar_file.mimes' => 'Bukti pembayaran harus JPG, JPEG, PNG, atau PDF',
            'bukti_bayar_file.max' => 'Ukuran bukti pembayaran maks 2MB',
        ];
    }
}
