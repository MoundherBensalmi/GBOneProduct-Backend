<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $table = 'positions';

    protected $fillable = [
        'name',
        'tr_name',
    ];

    public function people(): belongsToMany
    {
        return $this->belongsToMany(Person::class, 'people_positions', 'position_id', 'person_id');
    }
}
