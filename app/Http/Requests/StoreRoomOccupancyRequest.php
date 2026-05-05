<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
            'tanggal_keluar' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk',
                function ($attribute, $value, $fail) {
                    if (($this->tipe_sewa ?? null) === 'bulanan' && $value && $this->tanggal_masuk) {
                        $masuk = Carbon::parse($this->tanggal_masuk);
                        $keluar = Carbon::parse($value);
                        
                        // Calculate max checkout date: 5th of next applicable month
                        if ($masuk->day < 5) {
                            // If check-in before 5th, max is 5th of same month
                            $maxCheckout = Carbon::create($masuk->year, $masuk->month, 5);
                        } else {
                            // If check-in on/after 5th, max is 5th of next month
                            $maxCheckout = $masuk->copy()->addMonth()->day(5);
                        }
                        
                        if ($keluar->gt($maxCheckout)) {
                            $fail('Tanggal keluar tidak boleh melewati tanggal ' . $maxCheckout->format('d/m/Y') . '.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'room_id.required' => 'Pilih kamar',
            'consumer_id.required' => 'Pilih konsumen',
            'tipe_sewa.required' => 'Pilih tipe sewa (bulanan/harian)',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
        ];
    }
}
