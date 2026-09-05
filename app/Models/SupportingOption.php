<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportingOption extends Model
{
    protected $fillable = ['name'];

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_supporting_option');
    }
}