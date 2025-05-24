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
        'white_sorting_price',
        'yellow_sorting_price',
        'sorting_and_trimming_price',
        'sawing_price',
        'sorting_and_sawing_price',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class);
    }
}
