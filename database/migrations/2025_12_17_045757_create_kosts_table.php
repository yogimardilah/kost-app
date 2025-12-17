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
        Schema::create('kosts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kost');
            $table->text('alamat');

            $table->unsignedBigInteger('pemilik_id');
            $table->foreign('pemilik_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('harga')->nullable();
            $table->integer('jumlah_kamar')->default(0);
            $table->enum('status', ['tersedia', 'penuh'])->default('tersedia');

            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kosts');
    }
};
