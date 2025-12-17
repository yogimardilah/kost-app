<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        $ownerRole = DB::table('roles')->where('name', 'Owner')->first();

        if (!$adminRole || !$ownerRole) {
            throw new \Exception('Role Admin / Owner belum ada. Jalankan RoleSeeder dulu.');
        }

        DB::table('users')->insert([
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin Sistem',
                'email' => 'admin@kost.app',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $ownerRole->id,
                'name' => 'Owner Sistem',
                'email' => 'owner@kost.app',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
