<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'image_path' => [
                'string',
                'max:255',
            ],
            'programs' => [
                'array',
            ],
            'programs.*' => [
                'integer',
                'exists:programs,id'
            ],
        ];
    }
}
