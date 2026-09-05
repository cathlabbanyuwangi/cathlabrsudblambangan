<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientBhp extends Model
{
    use HasFactory;

    protected $table = 'patient_bhps';

    protected $fillable = [
        'patient_id',
        'receipt_number',
        'action_date',
        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
    ];

    /**
     * Relasi ke model Patient
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}