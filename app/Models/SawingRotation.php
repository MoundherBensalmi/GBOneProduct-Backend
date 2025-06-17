<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingRotation extends Model
{
    protected $fillable = [
        'sawing_station_id',
        'sawing_mission_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'is_initial' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function sawingStation(): BelongsTo
    {
        return $this->belongsTo(SawingStation::class)->withTrashed();
    }

    public function sawingMission(): BelongsTo
    {
        return $this->belongsTo(SawingMission::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'sawing_rotation_person')
            ->withTrashed()
            ->withPivot('amount')
            ->withTimestamps();    }
}
