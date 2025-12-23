<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addon_transaction_id');
            $table->unsignedBigInteger('addon_id')->nullable();
            $table->string('nama_addon'); // snapshot
            $table->integer('qty')->default(1);
            $table->decimal('harga', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('addon_transaction_id')->references('id')->on('addon_transactions')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('room_addons')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_transaction_details');
    }
};
