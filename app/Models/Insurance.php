<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = ['name'];

    /**
     * Relasi: Satu jaminan bisa dimiliki oleh banyak pasien.
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}