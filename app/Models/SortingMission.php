<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SortingMission extends Model
{
    protected $fillable = [
        'pay_period_id',
        'assigned_person_id',

        'date',
        'start_time',
        'end_time',

        'status',

        'yellow_sorting_price',
        'white_sorting_price',
        'trimming_price',
    ];

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class, 'pay_period_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_person_id')->withTrashed();
    }

    public function sortingRotations(): HasMany
    {
        return $this->hasMany(SortingRotation::class, 'sorting_mission_id');
    }
}
