<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->string('kontak_darurat_nama')->nullable()->after('kendaraan');
            $table->string('kontak_darurat_hubungan')->nullable()->after('kontak_darurat_nama');
            $table->string('kontak_darurat_no_hp', 20)->nullable()->after('kontak_darurat_hubungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->dropColumn(['kontak_darurat_nama', 'kontak_darurat_hubungan', 'kontak_darurat_no_hp']);
        });
    }
};
