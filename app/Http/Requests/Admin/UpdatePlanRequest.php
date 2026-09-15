<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role->value === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'price' => [
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required' => 'قیمت الزامی است.',
            'price.integer' => 'قیمت باید عدد باشد.',
            'price.min' => 'قیمت نمی‌تواند منفی باشد.',
        ];
    }
}
