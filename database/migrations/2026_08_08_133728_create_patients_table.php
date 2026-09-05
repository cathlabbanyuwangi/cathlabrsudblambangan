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
        Schema::create('patients', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('source'); // poli, rs_lain, mandiri
    $table->string('medical_record_number')->nullable()->unique();
    $table->string('name');
    $table->date('date_of_birth');
    $table->enum('gender', ['L', 'P']);
    $table->text('address');
    $table->string('regency'); // Kabupaten
    $table->string('district'); // Kecamatan
    $table->string('patient_phone')->nullable();
    $table->string('family_phone')->nullable();
    $table->foreignId('insurance_id')->constrained('insurances')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
