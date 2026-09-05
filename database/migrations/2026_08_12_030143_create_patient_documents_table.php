<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_documents', function (Blueprint $table) {
            $table->id();

            // Harus UUID karena patients.id menggunakan UUID
            $table->uuid('patient_id');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->string('document_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_documents');
    }
};