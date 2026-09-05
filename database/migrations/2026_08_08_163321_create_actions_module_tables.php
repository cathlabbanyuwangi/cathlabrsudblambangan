<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori
        Schema::create('action_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Tindakan
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_category_id')->constrained('action_categories');
            $table->string('name');
            $table->timestamps();
        });

        // 3. Dokter
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_category_id')->constrained('action_categories');
            $table->string('name');
            $table->timestamps();
        });

        // 4. Transaksi Tindakan (Menggunakan UUID untuk ID dan patient_id)
        Schema::create('action_records', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Diubah dari id() agar mendukung UUID string
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            
            $table->foreignId('action_category_id')->constrained('action_categories');
            $table->foreignId('action_id')->constrained('actions');
            $table->foreignId('doctor_id')->constrained('doctors');
            
            $table->string('origin_ward');
            $table->boolean('is_cito')->default(false);
            $table->integer('ring_count')->nullable();
            $table->string('diagnosis_1');
            $table->string('diagnosis_2')->nullable();
            $table->string('diagnosis_3')->nullable();
            $table->text('conclusion');
            $table->text('suggestion');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_records');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('actions');
        Schema::dropIfExists('action_categories');
    }
};