<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doctor extends Model
{
    protected $fillable = ['action_category_id', 'sub_division_id', 'name'];

    public function category(): BelongsTo { return $this->belongsTo(ActionCategory::class, 'action_category_id'); }
    public function subDivision(): BelongsTo { return $this->belongsTo(SubDivision::class); }
    
    // Relasi ke tindakan khusus / lintas kategori yang bisa dilakukan dokter ini
    public function actions(): BelongsToMany {
        return $this->belongsToMany(Action::class, 'doctor_action');
    }
}