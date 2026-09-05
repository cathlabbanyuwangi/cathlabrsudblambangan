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
    Schema::table('action_records', function (Blueprint $table) {
        // Jenis indikasi kasus (STEMI / NSTEMI / Elektif / Diagnostik)
        $table->string('indication_type')->default('Elective')->after('id'); 
        // Status keberhasilan prosedur
        $table->boolean('is_successful')->default(true)->after('conclusion');
        // Keterangan komplikasi jika ada
        $table->string('complication_notes')->nullable()->after('is_successful');
        // Durasi fluoroscopy (menit/detik) untuk audit radiasi
        $table->integer('fluoroscopy_time_minutes')->nullable()->after('complication_notes');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            //
        });
    }
};
