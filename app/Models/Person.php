<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Person extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'current_position_id',
        'name',
        'tr_name',
        'phone',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

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
}
