<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Waktu pasien tiba di RS/IGD (bisa ditaruh di tabel patients)
    Schema::table('patients', function (Blueprint $table) {
        $table->timestamp('arrived_hospital_at')->nullable()->after('status');
    });

    // Waktu tindakan inflasi balon di ruang Cathlab (ditaruh di action_records)
    Schema::table('action_records', function (Blueprint $table) {
        $table->timestamp('balloon_inflation_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system', function (Blueprint $table) {
            //
        });
    }
};
