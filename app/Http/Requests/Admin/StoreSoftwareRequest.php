<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoftwareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'download_url' => ['required', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام نرم‌افزار الزامی است.',
            'download_url.required' => 'لینک دانلود الزامی است.',
            'download_url.url' => 'لینک دانلود معتبر نیست.',
        ];
    }
}
