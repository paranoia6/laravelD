<?php

namespace App\Http\Requests\Admin;

use App\Models\Plan;
use App\Models\Support;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
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
            'device_type' => [
                'required',
                'integer',
                Rule::in([1, 2]),
            ],

            'account_type' => [
                'required',
                'integer',
                Rule::in([1, 2]),
            ],

            'plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
            ],

            'support_id' => [
                'required',
                'integer',

                Rule::exists('supports', 'id')
                    ->where(function ($query) {
                        $query
                            ->where(
                                'user_id',
                                auth()->id()
                            )
                            ->where(
                                'is_active',
                                true
                            );
                    }),
            ],

            'username_mode' => [
                'required',
                Rule::in([
                    'random',
                    'prefix',
                ]),
            ],

            'username_prefix' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[A-Za-z0-9]+$/',
                'required_if:username_mode,prefix',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'device_type.required' =>
                'نوع دستگاه را انتخاب کنید.',

            'device_type.in' =>
                'نوع دستگاه انتخاب‌شده معتبر نیست.',

            'account_type.required' =>
                'نوع اکانت را انتخاب کنید.',

            'account_type.in' =>
                'نوع اکانت انتخاب‌شده معتبر نیست.',

            'plan_id.required' =>
                'پلن را انتخاب کنید.',

            'plan_id.exists' =>
                'پلن انتخاب‌شده وجود ندارد.',

            'support_id.required' =>
                'پشتیبانی را انتخاب کنید.',

            'support_id.exists' =>
                'این پشتیبانی ثبت نشده، غیرفعال است یا متعلق به حساب شما نیست.',

            'username_mode.required' =>
                'روش ساخت نام کاربری را انتخاب کنید.',

            'username_mode.in' =>
                'روش ساخت نام کاربری معتبر نیست.',

            'username_prefix.required_if' =>
                'پیشوند نام کاربری الزامی است.',

            'username_prefix.regex' =>
                'پیشوند فقط باید شامل حروف انگلیسی و اعداد باشد.',

            'quantity.required' =>
                'تعداد اکانت را وارد کنید.',

            'quantity.min' =>
                'حداقل تعداد اکانت ۱ است.',

            'quantity.max' =>
                'حداکثر تعداد اکانت ۱۰۰۰ است.',
        ];
    }

    protected function passedValidation(): void
    {
        $plan = Plan::find(
            $this->integer('plan_id')
        );

        if (! $plan) {
            return;
        }

        if (! $plan->is_active) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'plan_id' =>
                            'این پلن در حال حاضر غیرفعال است.',
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

        $expectedType =
            $this->integer('account_type') === 2
                ? 'special'
                : 'normal';

        if ($plan->type !== $expectedType) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'plan_id' =>
                            'پلن انتخاب‌شده با نوع اکانت هماهنگ نیست.',
                    ])
            );
        }

        /*
         * Support باید واقعاً حداقل یک لینک فعال داشته باشد.
         */
        $support = Support::query()
            ->where('id', $this->integer('support_id'))
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (! $support || ! $support->hasLinks()) {
            abort(
                redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'support_id' =>
                            'برای پشتیبانی انتخاب‌شده هنوز اطلاعاتی ثبت نشده است.',
                    ])
            );
        }
    }
}
