<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingMission extends Model
{
    protected $fillable = [
        'pay_period_id',
        'assigned_person_id',

        'date',
        'start_time',
        'end_time',

        'status',
    ];

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class, 'pay_period_id');
    }

    public function sawingRotations(): HasMany
    {
        return $this->hasMany(SawingRotation::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_person_id')->withTrashed();
    }
}
