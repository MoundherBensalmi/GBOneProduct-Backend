<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitSawingStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mission_id' => 'required|integer',
            'station_id' => [
                'required',
                'integer',
                Rule::exists('sawing_stations', 'id')->whereNull('deleted_at'),
            ],
            'people' => 'required|array',
            'people.*' => [
                'required',
                'integer',
                Rule::exists('people', 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
