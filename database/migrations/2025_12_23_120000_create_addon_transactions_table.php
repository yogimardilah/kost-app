<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->date('tanggal')->nullable();
            $table->string('status')->default('pending'); // pending, posted, canceled
            $table->decimal('total', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('consumer_id')->references('id')->on('consumers')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_transactions');
    }
};
