<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    protected $fillable = ['action_category_id', 'name'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ActionCategory::class, 'action_category_id');
    }
}