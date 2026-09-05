<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_priority' => 'boolean',
    ];

    // Konstanta status agar tidak salah ketik (typo) di controller/view
    public const STATUS_BERSEDIA = 'bersedia';
    public const STATUS_PERNAH_TINDAKAN = 'pernah_tindakan';
    public const STATUS_MENOLAK = 'menolak';

    // --- HELPER METHODS UNTUK STATUS (Agar kode di Blade rapi) ---

    public function isBersedia(): bool
    {
        return $this->status === self::STATUS_BERSEDIA;
    }

    public function isPernahTindakan(): bool
    {
        return $this->status === self::STATUS_PERNAH_TINDAKAN;
    }

    public function isMenolak(): bool
    {
        return $this->status === self::STATUS_MENOLAK;
    }

    // --- RELASI ---

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function supportingOptions(): BelongsToMany
    {
        return $this->belongsToMany(SupportingOption::class, 'patient_supporting_option');
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'called_by');
    }

    public function actionRecords(): HasMany
    {
        return $this->hasMany(ActionRecord::class);
    }

    public function bhps(): HasMany
    {
        return $this->hasMany(PatientBhp::class, 'patient_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class);
    }
}