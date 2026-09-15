<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role->value === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'config' => ['required', 'string'],
            'internet_type' => ['required', 'integer', Rule::in([1, 2, 3])],
            'account_type' => ['required', 'integer', Rule::in([1, 2])],
            'descriptions' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'config.required' => 'محتوای کانفیگ الزامی است.',
            'internet_type.required' => 'نوع اینترنت را انتخاب کنید.',
            'internet_type.in' => 'نوع اینترنت باید بین ۱ تا ۳ باشد.',
            'account_type.required' => 'نوع کانفیگ را انتخاب کنید.',
            'account_type.in' => 'نوع کانفیگ نامعتبر است.',
            'descriptions.required' => 'توضیحات الزامی است.',
        ];
    }
}
