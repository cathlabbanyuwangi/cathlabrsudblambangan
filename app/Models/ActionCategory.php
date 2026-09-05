<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Action;
use App\Models\Doctor;
use App\Models\ActionRecord;

class ActionCategory extends Model
{
    protected $fillable = ['name'];

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function actionRecords(): HasMany
    {
        return $this->hasMany(ActionRecord::class);
    }
}