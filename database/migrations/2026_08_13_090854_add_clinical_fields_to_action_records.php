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
    Schema::table('action_records', function (Blueprint $table) {
        $table->integer('timi_flow_post')->nullable(); // Nilai 0-3
        $table->decimal('residual_stenosis', 5, 2)->nullable();
        $table->string('complication_type')->nullable(); // Hematoma, Arrhythmia, dll
        $table->integer('contrast_volume')->nullable(); // dalam ml
        $table->decimal('fluro_time', 8, 2)->nullable(); // dalam menit
        $table->boolean('is_mace')->default(false);
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
