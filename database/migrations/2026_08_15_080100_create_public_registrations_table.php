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
        Schema::create('public_registrations', function (Blueprint $table) {
            $table->id();
            
            // Section 1: Administrasi & Rujukan
            $table->string('source')->nullable(); // poli, rs_lain, mandiri
            $table->string('medical_record_number')->nullable();
            $table->string('origin_hospital')->nullable();
            $table->string('origin_hospital_custom')->nullable();
            
            // Section 2: Identitas & Kontak
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable(); // L / P
            $table->unsignedBigInteger('insurance_id')->nullable();
            $table->string('patient_phone');
            $table->string('family_phone')->nullable();
            
            // Section 3: Wilayah & Alamat
            $table->string('regency')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            
            // Section 4: Penunjang Medis (Disimpan sebagai JSON karena bentuknya array checkbox)
            $table->json('supporting_options')->nullable();
            
            // Section 5: Keterangan / Catatan
            $table->text('notes')->nullable();
            
            // Status Moderasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_registrations');
    }
};