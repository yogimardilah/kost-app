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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consumer_id')->constrained('consumers')->cascadeOnDelete();

            $table->foreignId('billing_id')->nullable()->constrained('billings')->nullOnDelete();
            $table->foreignId('billing_detail_id')->nullable()->constrained('billing_details')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('occupancy_id')->nullable()->constrained('room_occupancies')->nullOnDelete();

            $table->timestamp('tanggal');
            $table->enum('tipe', ['debit', 'kredit']);
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan');
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['consumer_id', 'tanggal']);
            $table->unique('billing_detail_id');
            $table->unique('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
