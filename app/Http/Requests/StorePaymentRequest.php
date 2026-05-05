<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Billing;
use App\Models\Payment;

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
            'jumlah' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $billingId = $this->input('billing_id');
                    if (!$billingId) {
                        return;
                    }

                    $billing = Billing::find($billingId);
                    if (!$billing) {
                        return;
                    }

                    $totalTagihan = round((float) ($billing->total_tagihan ?? 0), 0);
                    $totalPaid = round((float) Payment::where('billing_id', $billingId)->sum('jumlah'), 0);
                    $remaining = max(0, $totalTagihan - $totalPaid);
                    $requested = round((float) $value, 0);

                    if ($remaining <= 0) {
                        $fail('Tagihan ini sudah lunas dan tidak dapat menerima pembayaran baru.');
                        return;
                    }

                    if ($requested > $remaining) {
                        $fail('Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').');
                    }
                },
            ],
            'metode' => 'required|in:tunai,transfer,qris',
            'bukti_bayar' => 'nullable|string',
            'bukti_bayar_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
            'metode.in' => 'Metode pembayaran harus salah satu dari: tunai, transfer, qris',
            'bukti_bayar_file.required' => 'Upload bukti pembayaran wajib diisi',
            'bukti_bayar_file.file' => 'Bukti pembayaran harus berupa file',
            'bukti_bayar_file.mimes' => 'Bukti pembayaran harus JPG, JPEG, PNG, atau PDF',
            'bukti_bayar_file.max' => 'Ukuran bukti pembayaran maks 2MB',
        ];
    }
}
