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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->integer('bulan')->comment('1-12');
            $table->integer('tahun');
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('total_gaji', 15, 2);
            $table->dateTime('tanggal_bayar')->nullable();
            $table->enum('status', ['pending', 'dibayar'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable()->comment('Path to uploaded file');
            $table->timestamps();
            
            // Prevent duplicate payroll for same employee in same period
            $table->unique(['employee_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
