<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DicomStudy extends Model
{
    use HasFactory;

    protected $table = 'dicom_studies';

    protected $fillable = [
        'patient_id',
        'orthanc_study_id',
        'study_instance_uid',
        'study_date',
        'study_time',
        'accession_number',
        'study_description',
        'modality',
        'dicom_patient_id',
        'dicom_patient_name',
        'series_count',
        'instance_count',
        'import_status',
        'import_error',
        'uploaded_by',
    ];

    protected $casts = [
        'study_date' => 'date',
        'series_count' => 'integer',
        'instance_count' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE PASIEN
    |--------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USER YANG MELAKUKAN UPLOAD
    |--------------------------------------------------------------------------
    */

    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by',
            'id'
        );
    }
}