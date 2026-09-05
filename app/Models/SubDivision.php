<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubDivision extends Model
{
    protected $fillable = ['action_category_id', 'name'];

    public function category(): BelongsTo { return $this->belongsTo(ActionCategory::class, 'action_category_id'); }
    public function doctors(): HasMany { return $this->hasMany(Doctor::class); }
}