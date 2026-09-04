<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'android_id' => $this->header('X-Android-Id'),
            'device_model' => $this->header('X-Device-Model'),
            'os_version' => $this->header('X-Os-Version'),
            'manufacturer' => $this->header('X-Manufacturer'),
            'app_version' => $this->header('X-App-Version'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'regex:/^[a-zA-Z0-9]+$/',
            ],

            'password' => [
                'required',
                'string',
            ],

            'android_id' => [
                'required',
                'string',
            ],

            'device_model' => [
                'required',
                'string',
            ],

            'os_version' => [
                'required',
                'string',
            ],

            'manufacturer' => [
                'required',
                'string',
            ],

            'app_version' => [
                'required',
                'string',
            ],
        ];
    }
}
