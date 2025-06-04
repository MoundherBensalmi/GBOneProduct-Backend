<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingMission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_session_id',

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
}
