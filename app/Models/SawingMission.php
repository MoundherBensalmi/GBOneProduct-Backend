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
        'assigned_user_id',

        'date',
        'start_time',
        'end_time',

        'is_started',
        'is_finished',
    ];

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class, 'pay_period_id');
    }

    public function sawingRotations(): HasMany
    {
        return $this->hasMany(SawingRotation::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->withTrashed();
    }
}
