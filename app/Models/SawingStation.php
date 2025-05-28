<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SawingStation extends Model
{
    use SoftDeletes;

    protected $table = 'sawing_stations';

    protected $fillable = [
        'name',
    ];

    public function rotations(): HasMany
    {
        return $this->hasMany(SawingRotation::class);
    }
}
