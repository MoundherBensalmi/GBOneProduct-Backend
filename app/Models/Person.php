<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'current_position_id',
        'name',
        'tr_name',
        'phone',
    ];

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'people_positions', 'person_id', 'position_id')
            ->withPivot('start_date', 'end_date', 'payment_type', 'salary')
            ->withTimestamps();
    }

    public function currentPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'current_position_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'person_id', 'id')->latestOfMany();
    }
}
