<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Sub-Divisi (SPJP, SBTKV, Radiologi Intervensi, dll)
        Schema::create('sub_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_category_id')->constrained('action_categories')->onDelete('cascade');
            $table->string('name'); // Contoh: SPJP, SPN, Radiologi Intervensi
            $table->timestamps();
        });

        // Update Tabel Doctors agar punya sub_division_id
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('sub_division_id')->nullable()->constrained('sub_divisions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['sub_division_id']);
            $table->dropColumn('sub_division_id');
        });
        Schema::dropIfExists('sub_divisions');
    }
};