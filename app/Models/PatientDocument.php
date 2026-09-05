<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'document_name',
        'file_path',
        'file_type',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}