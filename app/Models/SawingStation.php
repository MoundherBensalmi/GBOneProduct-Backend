<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingStation extends Model
{
    use SoftDeletes;

    protected $table = 'sawing_stations';

    protected $fillable = [
        'name',
    ];
}
