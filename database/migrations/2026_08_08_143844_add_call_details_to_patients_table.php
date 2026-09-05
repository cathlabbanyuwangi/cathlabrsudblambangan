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
        $table->string('status')->default('waiting')->after('is_priority'); // waiting, called
        $table->enum('willingness', ['bersedia', 'tidak_bersedia'])->nullable()->after('status');
        $table->foreignUuid('called_by')->nullable()->constrained('users')->nullOnDelete()->after('willingness');
        $table->dateTime('scheduled_at')->nullable()->after('called_by');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            //
        });
    }
};
