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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->foreignId('consumer_id')->constrained('consumers');
            $table->foreignId('room_id')->constrained('rooms');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->decimal('total_tagihan', 12, 2);
            $table->string('status', 20);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
