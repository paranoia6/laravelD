<?php

namespace App\Http\Requests\Admin;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RenewAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && in_array(
                auth()->user()->role->value,
                ['admin', 'super_admin'],
                true
            )
            && (bool) auth()->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('plans', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'پلن تمدید را انتخاب کنید.',
            'plan_id.integer' => 'پلن انتخاب‌شده معتبر نیست.',
            'plan_id.exists' => 'پلن انتخاب‌شده وجود ندارد.',
        ];
    }

    protected function passedValidation(): void
    {
        $plan = Plan::query()
            ->whereKey($this->integer('plan_id'))
            ->first();

        if (! $plan) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'plan_id' => 'پلن انتخاب‌شده وجود ندارد.',
                    ])
            );
        }

        if (! $plan->is_active) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'plan_id' => 'این پلن در حال حاضر غیرفعال است.',
                    ])
            );
        }

        if ($plan->price === null) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'plan_id' =>
                            'قیمت این پلن هنوز توسط Super Admin تعیین نشده است.',
                    ])
            );
        }
    }
}
