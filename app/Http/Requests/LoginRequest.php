<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $value = (string) $value;

                    $isUsername = preg_match(
                        '/^[A-Za-z0-9]+$/',
                        $value
                    );

                    $isEmail = filter_var(
                        $value,
                        FILTER_VALIDATE_EMAIL
                    );

                    if (! $isUsername && ! $isEmail) {
                        $fail(
                            'نام کاربری فقط باید شامل حروف انگلیسی و اعداد باشد.'
                        );
                    }
                },
            ],

            'password' => [
                'required',
                'string',
            ],

            'android_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'os_version' => [
                'nullable',
                'string',
                'max:255',
            ],

            'device_model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'manufacturer' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' =>
                'نام کاربری الزامی است.',

            'password.required' =>
                'کلمه عبور الزامی است.',
        ];
    }
}
