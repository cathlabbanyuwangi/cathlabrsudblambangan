<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_bhps', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('action_date')->nullable(); // Tanggal tindakan/nota
            $table->string('item_name');             // Nama obat/alkes
            $table->decimal('quantity', 10, 2);      // Jumlah
            $table->string('unit')->nullable();      // Satuan (Pcs, Ampul, Box, dll)
            $table->decimal('unit_price', 15, 2);    // Harga satuan
            $table->decimal('subtotal', 15, 2);      // Subtotal harga
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_bhps');
    }
};