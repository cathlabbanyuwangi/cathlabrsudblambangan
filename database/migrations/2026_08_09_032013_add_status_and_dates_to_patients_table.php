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
        Schema::table('patients', function (Blueprint $table) {
            // Karena 'status' sudah ada, kita hanya tambahkan tanggal tindakan & penolakan
            if (!Schema::hasColumn('patients', 'action_date')) {
                $table->date('action_date')->nullable();
            }
            if (!Schema::hasColumn('patients', 'rejection_date')) {
                $table->date('rejection_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['action_date', 'rejection_date']);
        });
    }
};