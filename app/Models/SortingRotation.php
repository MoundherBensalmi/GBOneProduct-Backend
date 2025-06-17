<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SortingRotation extends Model
{
    protected $fillable = [
        'sorting_mission_id',
        'type',
        'amount',
    ];
}
