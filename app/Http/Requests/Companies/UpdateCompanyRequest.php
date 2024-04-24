<?php

namespace App\Http\Requests\Companies;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'max:255',
            ],
            'image_path' => [
                'string',
                'max:255',
            ],
            'location' => [
                'string',
                'max:255',
            ],
            'industry' => [
                'string',
                'max:255',
            ],
            'user_id' => [
                'integer',
                'exists:users,id'
            ],
        ];
    }
}
