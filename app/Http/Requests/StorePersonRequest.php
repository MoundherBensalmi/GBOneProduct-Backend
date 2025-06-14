<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tr_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_position_id' => 'nullable|exists:positions,id',

            'create_user' => 'boolean',
            'username' => 'required_if:create_user,true|string|max:255|unique:users,username',
            'password' => 'required_if:create_user,true|string|min:3',
            'role' => 'required_if:create_user,true|in:user,admin',
        ];
    }
}
