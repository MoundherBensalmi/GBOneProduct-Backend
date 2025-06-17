<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SortingRotation extends Model
{
    protected $fillable = [
        'sorting_mission_id',
        'person_id',
        'type',
        'amount',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(SawingMission::class, 'sorting_mission_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id')->withTrashed();
    }
}
