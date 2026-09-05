<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            if (!Schema::hasColumn('action_records', 'sub_division_id')) {
                $table->unsignedBigInteger('sub_division_id')->nullable();
            }
            if (!Schema::hasColumn('action_records', 'anesthesia_doctor_id')) {
                $table->unsignedBigInteger('anesthesia_doctor_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            $table->dropColumn(['sub_division_id', 'anesthesia_doctor_id']);
        });
    }
};