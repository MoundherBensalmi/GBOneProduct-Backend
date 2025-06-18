<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'start_date',
        'end_date',
        'is_active',
        'white_sorting_price',
        'yellow_sorting_price',
        'trimming_price',
        'yellow_sawing_price',
        'white_sawing_price',
        'yellow_sorting_and_sawing_price',
        'white_sorting_and_sawing_price'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function sawingMissions(): HasMany
    {
        return $this->hasMany(SawingMission::class, 'pay_period_id', 'id');
    }

    public function sortingMissions(): HasMany
    {
        return $this->hasMany(SortingMission::class, 'pay_period_id', 'id');
    }
}
