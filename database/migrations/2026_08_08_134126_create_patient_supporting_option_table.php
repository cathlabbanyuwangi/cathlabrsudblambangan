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
       Schema::create('patient_supporting_option', function (Blueprint $table) {
    $table->id();
    $table->foreignUuid('patient_id')->constrained()->onDelete('cascade');
    $table->foreignId('supporting_option_id')->constrained()->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_supporting_option');
    }
};
