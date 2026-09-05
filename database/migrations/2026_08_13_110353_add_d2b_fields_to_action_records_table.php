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
            // Kolom is_cito dilewati karena sudah ada di database
            $table->string('diagnosis_d2b')->nullable();

            // 15 Tahapan Kegiatan Door-to-Balloon Time[cite: 1, 2]
            $steps = [
                'igd', 'triage', 'ecg', 'assessment', 'diagnosis_est', 
                'ppci_consult', 'family_info', 'family_approval', 
                'to_cathlab', 'arrival_cathlab', 'proc_start', 
                'other_action', 'balloon_dilatation', 'proc_finish', 'room_transfer'
            ];

            foreach ($steps as $step) {
                $table->dateTime("d2b_{$step}_time")->nullable();
                $table->string("d2b_{$step}_officer")->nullable();
                $table->text("d2b_{$step}_notes")->nullable();
            }

            // Catatan Umum & Verifikasi Ka Instalasi/Divisi[cite: 1, 2]
            $table->text('d2b_general_notes')->nullable();
            $table->string('d2b_verified_name')->nullable();
            $table->string('d2b_verified_nip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_records', function (Blueprint $table) {
            $table->dropColumn([
                'diagnosis_d2b',
                'd2b_igd_time', 'd2b_igd_officer', 'd2b_igd_notes',
                'd2b_triage_time', 'd2b_triage_officer', 'd2b_triage_notes',
                'd2b_ecg_time', 'd2b_ecg_officer', 'd2b_ecg_notes',
                'd2b_assessment_time', 'd2b_assessment_officer', 'd2b_assessment_notes',
                'd2b_diagnosis_est_time', 'd2b_diagnosis_est_officer', 'd2b_diagnosis_est_notes',
                'd2b_ppci_consult_time', 'd2b_ppci_consult_officer', 'd2b_ppci_consult_notes',
                'd2b_family_info_time', 'd2b_family_info_officer', 'd2b_family_info_notes',
                'd2b_family_approval_time', 'd2b_family_approval_officer', 'd2b_family_approval_notes',
                'd2b_to_cathlab_time', 'd2b_to_cathlab_officer', 'd2b_to_cathlab_notes',
                'd2b_arrival_cathlab_time', 'd2b_arrival_cathlab_officer', 'd2b_arrival_cathlab_notes',
                'd2b_proc_start_time', 'd2b_proc_start_officer', 'd2b_proc_start_notes',
                'd2b_other_action_time', 'd2b_other_action_officer', 'd2b_other_action_notes',
                'd2b_balloon_dilatation_time', 'd2b_balloon_dilatation_officer', 'd2b_balloon_dilatation_notes',
                'd2b_proc_finish_time', 'd2b_proc_finish_officer', 'd2b_proc_finish_notes',
                'd2b_room_transfer_time', 'd2b_room_transfer_officer', 'd2b_room_transfer_notes',
                'd2b_general_notes',
                'd2b_verified_name',
                'd2b_verified_nip',
            ]);
        });
    }
};