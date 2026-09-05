<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionRecord extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'action_records';

    protected $fillable = [
        'patient_id', 
        'action_category_id', 
        'sub_division_id', 
        'action_id', 
        'doctor_id', 
        'anesthesia_doctor_id',
        'action_date', 
        'origin_ward', 
        'is_cito', 
        'ring_count', 
        'diagnosis_1', 
        'diagnosis_2', 
        'diagnosis_3', 
        'conclusion', 
        'suggestion', 
        'notes',
        'timi_flow_post', 
        'residual_stenosis', 
        'complication_type', 
        'contrast_volume', 
        'fluro_time', 
        'is_mace',
        
        // --- Kolom Tambahan Lembar Audit Door-to-Balloon (D2B) ---
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
    ];

    // Tambahkan casts agar tanggal otomatis dikenali sebagai format DateTime
    protected $casts = [
        'action_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true; 

    // Relasi
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function category(): BelongsTo { return $this->belongsTo(ActionCategory::class, 'action_category_id'); }
    public function action(): BelongsTo { return $this->belongsTo(Action::class); }
    public function doctor(): BelongsTo { return $this->belongsTo(Doctor::class); }
}