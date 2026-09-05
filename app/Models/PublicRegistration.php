<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'medical_record_number',
        'origin_hospital',
        'origin_hospital_custom',
        'name',
        'date_of_birth',
        'gender',
        'insurance_id',
        'patient_phone',
        'family_phone',
        'regency',
        'district',
        'address',
        'supporting_options',
        'notes',
        'status',
    ];

    protected $casts = [
        'supporting_options' => 'array',
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }
}