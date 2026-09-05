<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->dateTime('document_date')
                ->nullable()
                ->after('file_type');
        });

        // Isi dokumen lama menggunakan created_at
        DB::table('patient_documents')
            ->whereNull('document_date')
            ->update([
                'document_date' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->dropColumn('document_date');
        });
    }
};