<?php

namespace App\Http\Requests\Programs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'string',
                'max:255',
            ],
            'description' => [
                'string',
            ],
            'start_date' => [
                'date',
            ],
            'end_date' => [
                'date',
            ],
            'user_id' => [
                'integer',
                'exists:users,id'
            ],
        ];
    }
}
