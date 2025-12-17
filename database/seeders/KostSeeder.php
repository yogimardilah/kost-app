<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KostSeeder extends Seeder
{
    public function run(): void
    {
        $owner = DB::table('users')
            ->where('email', 'owner@kost.app')
            ->first();

        DB::table('kosts')->insert([
            'nama_kost' => 'Kost Maju Jaya',
            'alamat' => 'Jl. Melati No. 10, Bandung',
            'pemilik_id' => $owner->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
