<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SortingMission extends Model
{
    protected $fillable = [
        'pay_period_id',
        'assigned_user_id',

        'date',
        'start_time',
        'end_time',

        'status',
    ];

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class, 'pay_period_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withTrashed();
    }

    public function sortingRotations(): HasMany
    {
        return $this->hasMany(SortingRotation::class, 'sorting_mission_id');
    }
}
