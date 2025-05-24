<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingMission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_session_id',
    ];

    public function workSession(): BelongsTo
    {
        return $this->belongsTo(WorkSession::class);
    }

    public function sawingRotations(): HasMany
    {
        return $this->hasMany(SawingRotation::class);
    }
}
