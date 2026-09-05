<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            // Cek agar tidak duplikat jika kolomnya setengah jadi
            if (!Schema::hasColumn('action_records', 'action_date')) {
                $table->dateTime('action_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            $table->dropColumn('action_date');
        });
    }
};