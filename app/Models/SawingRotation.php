<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingRotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sawing_mission_id',
        'is_initial',
        'amount',
    ];

    protected $casts = [
        'is_initial' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function sawingMission(): BelongsTo
    {
        return $this->belongsTo(SawingMission::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'sawing_rotation_person')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
