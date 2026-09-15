@extends('layouts.admin')

@section('title', 'ایجاد اکانت')

@section('content')

    <div class="admin-page">

        <div class="page-header">

            <div>

                <h1>
                    ایجاد اکانت
                </h1>

                <p>
                    ایجاد یک یا چند اکانت برای مشتری
                </p>

            </div>


            <a
                href="{{ route('admin.accounts') }}"
                class="btn btn-light"
            >
                لیست اکانت‌ها
            </a>

        </div>


        @if($errors->any())

            <div class="alert alert-danger">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('admin.accounts.store') }}"
            id="account-create-form"
        >

            @csrf


            <div class="account-grid">


                {{-- DEVICE --}}
                <div class="form-card">

                    <h3>
                        نوع دستگاه
                    </h3>


                    <div class="choices">

                        <label class="choice">

                            <input
                                type="radio"
                                name="device_type"
                                value="1"
                                {{ old(
                                    'device_type',
                                    '1'
                                ) == '1'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                Android
                            </b>

                            <small>
                                دستگاه اندرویدی
                            </small>

                        </span>

                        </label>


                        <label class="choice">

                            <input
                                type="radio"
                                name="device_type"
                                value="2"
                                {{ old(
                                    'device_type'
                                ) == '2'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                iPhone
                            </b>

                            <small>
                                دستگاه آیفون
                            </small>

                        </span>

                        </label>

                    </div>

                </div>


                {{-- ACCOUNT TYPE --}}
                <div class="form-card">

                    <h3>
                        نوع اکانت
                    </h3>


                    <div class="choices">

                        <label class="choice">

                            <input
                                type="radio"
                                name="account_type"
                                value="1"
                                {{ old(
                                    'account_type',
                                    '1'
                                ) == '1'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                عادی
                            </b>

                            <small>
                                پلن عادی
                            </small>

                        </span>

                        </label>


                        <label class="choice">

                            <input
                                type="radio"
                                name="account_type"
                                value="2"
                                {{ old(
                                    'account_type'
                                ) == '2'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                ویژه
                            </b>

                            <small>
                                پلن ویژه
                            </small>

                        </span>

                        </label>

                    </div>

                </div>


                {{-- PLAN --}}
                <div class="form-card">

                    <h3>
                        پلن و قیمت
                    </h3>


                    <select
                        id="duration_months"
                        class="form-control"
                        required
                    >

                        <option value="">
                            انتخاب مدت
                        </option>


                        @foreach(
                            $plans
                                ->pluck('duration_months')
                                ->unique()
                                ->sort()
                            as $months
                        )

                            <option
                                value="{{ $months }}"
                                {{ old(
                                    'duration_months'
                                ) == $months
                                    ? 'selected'
                                    : ''
                                }}
                            >

                                @if($months == 1)
                                    ۱ ماهه
                                @elseif($months == 2)
                                    ۲ ماهه
                                @elseif($months == 3)
                                    ۳ ماهه
                                @else
                                    {{ $months }} ماهه
                                @endif

                            </option>

                        @endforeach

                    </select>


                    <input
                        type="hidden"
                        name="plan_id"
                        id="plan_id"
                        value="{{ old('plan_id') }}"
                    >


                    <div
                        id="plan-price"
                        class="price-box"
                        style="display:none"
                    >

                        قیمت هر اکانت:

                        <strong id="price-value">
                            ۰
                        </strong>

                        تومان

                    </div>


                    <div
                        id="total-price"
                        class="price-box"
                        style="display:none"
                    >

                        مبلغ کل:

                        <strong id="total-price-value">
                            ۰
                        </strong>

                        تومان

                    </div>


                    <div
                        id="plan-error"
                        class="error-text"
                        style="display:none"
                    ></div>


                    <div class="balance-box">

                        @if($isSuperAdmin)

                            <small>
                                Super Admin محدودیت موجودی ندارد.
                            </small>

                        @else

                            <small>
                                موجودی فعلی:
                            </small>

                            <strong>
                                {{ number_format(
                                    $currentBalance
                                ) }}

                                تومان
                            </strong>

                        @endif

                    </div>

                </div>


                {{-- SUPPORT --}}
                <div class="form-card">

                    <h3>
                        پشتیبانی
                    </h3>


                    <select
                        name="support_id"
                        class="form-control"
                        required
                    >

                        <option value="">
                            انتخاب پشتیبانی
                        </option>


                        @forelse($supports as $support)

                            <option
                                value="{{ $support->id }}"
                                {{ old(
                                    'support_id'
                                ) == $support->id
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                {{ $support->name }}
                            </option>

                        @empty

                            <option value="" disabled>
                                هنوز پشتیبانی برای حساب شما ثبت نشده است.
                            </option>

                        @endforelse

                    </select>


                    @if($supports->isEmpty())

                        <small class="help">
                            ابتدا Support متعلق به حساب خودتان ایجاد کنید.
                        </small>

                    @endif

                </div>


                {{-- USERNAME --}}
                <div class="form-card full">

                    <h3>
                        روش نام کاربری
                    </h3>


                    <div class="choices">


                        <label class="choice">

                            <input
                                type="radio"
                                name="username_mode"
                                value="random"
                                {{ old(
                                    'username_mode',
                                    'random'
                                ) == 'random'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                تصادفی
                            </b>

                            <small>
                                سیستم نام کاربری می‌سازد
                            </small>

                        </span>

                        </label>


                        <label class="choice">

                            <input
                                type="radio"
                                name="username_mode"
                                value="prefix"
                                {{ old(
                                    'username_mode'
                                ) == 'prefix'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>

                            <b>
                                نام پایه
                            </b>

                            <small>
                                مثلاً test001 تا test010
                            </small>

                        </span>

                        </label>

                    </div>


                    <div
                        id="prefix-box"
                        style="
                        display:none;
                        margin-top:18px;
                    "
                    >

                        <label class="label">
                            نام پایه
                        </label>


                        <input
                            type="text"
                            name="username_prefix"
                            id="username_prefix"
                            class="form-control"
                            value="{{ old(
                            'username_prefix'
                        ) }}"
                            placeholder="مثلاً test"
                            maxlength="30"
                            autocomplete="off"
                            inputmode="latin"
                        >


                        <small class="help">
                            فقط حروف انگلیسی و اعداد مجاز است.
                        </small>

                    </div>

                </div>


                {{-- QUANTITY --}}
                <div class="form-card">

                    <h3>
                        تعداد اکانت
                    </h3>


                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        class="form-control"
                        value="{{ old(
                        'quantity',
                        1
                    ) }}"
                        min="1"
                        max="1000"
                        required
                    >


                    <small class="help">
                        حداکثر ۱۰۰۰ اکانت
                    </small>

                </div>


                {{-- SUMMARY --}}
                <div class="form-card">

                    <h3>
                        خلاصه
                    </h3>


                    <div class="summary">

                        <div>

                        <span>
                            دستگاه
                        </span>

                            <b id="summary-device">
                                Android
                            </b>

                        </div>


                        <div>

                        <span>
                            نوع
                        </span>

                            <b id="summary-type">
                                عادی
                            </b>

                        </div>


                        <div>

                        <span>
                            مدت
                        </span>

                            <b id="summary-duration">
                                -
                            </b>

                        </div>


                        <div>

                        <span>
                            تعداد
                        </span>

                            <b id="summary-quantity">
                                1
                            </b>

                        </div>

                    </div>

                </div>


            </div>


            <div class="actions">

                <a
                    href="{{ route('admin.accounts') }}"
                    class="btn btn-light"
                >
                    انصراف
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="submit-btn"
                >
                    ایجاد اکانت
                </button>

            </div>


        </form>

    </div>


    <style>

        .account-grid {
            display: grid;
            grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
            gap: 18px;
        }

        .form-card {
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 18px;
            padding: 22px;
            box-shadow:
                0 5px 18px
                rgba(120, 60, 90, .06);
        }

        .form-card.full {
            grid-column: 1 / -1;
        }

        .form-card h3 {
            margin-top: 0;
            margin-bottom: 18px;
        }

        .choices {
            display: grid;
            grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
            gap: 12px;
        }

        .choice {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #eadde4;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
        }

        .choice:hover {
            border-color: #c7a5b7;
        }

        .choice input {
            flex-shrink: 0;
        }

        .choice span {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .choice small {
            opacity: .7;
        }

        .form-control {
            width: 100%;
            box-sizing: border-box;
        }

        .price-box {
            margin-top: 15px;
            padding: 14px;
            border-radius: 12px;
            background: #f8f3f6;
        }

        .price-box strong {
            font-size: 18px;
            margin: 0 5px;
        }

        .error-text {
            margin-top: 12px;
            color: #c62828;
        }

        .help {
            display: block;
            margin-top: 8px;
            opacity: .7;
        }

        .balance-box {
            margin-top: 14px;
            padding: 12px;
            border-radius: 10px;
            background: #f8f8f8;
        }

        .summary {
            display: grid;
            gap: 12px;
        }

        .summary > div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .summary span {
            opacity: .7;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        @media(max-width: 900px) {

            .account-grid {
                grid-template-columns: 1fr;
            }

            .form-card.full {
                grid-column: auto;
            }

        }

        @media(max-width: 600px) {

            .choices {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                width: 100%;
            }

        }

    </style>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const plans =
                    {{ Illuminate\Support\Js::from(
                        $plans
                            ->map(
                                fn ($plan) => [
                                    'id' =>
                                        $plan->id,

                                    'type' =>
                                        $plan->type,

                                    'duration_months' =>
                                        (int)
                                        $plan->duration_months,

                                    'price' =>
                                        (int)
                                        $plan->price,

                                    'is_active' =>
                                        (bool)
                                        $plan->is_active,
                                ]
                            )
                            ->values()
                    ) }};


                const durationSelect =
                    document.getElementById(
                        'duration_months'
                    );


                const planIdInput =
                    document.getElementById(
                        'plan_id'
                    );


                const accountTypeInputs =
                    document.querySelectorAll(
                        'input[name="account_type"]'
                    );


                const deviceInputs =
                    document.querySelectorAll(
                        'input[name="device_type"]'
                    );


                const quantityInput =
                    document.getElementById(
                        'quantity'
                    );


                const priceBox =
                    document.getElementById(
                        'plan-price'
                    );


                const priceValue =
                    document.getElementById(
                        'price-value'
                    );


                const totalPriceBox =
                    document.getElementById(
                        'total-price'
                    );


                const totalPriceValue =
                    document.getElementById(
                        'total-price-value'
                    );


                const planError =
                    document.getElementById(
                        'plan-error'
                    );


                const summaryDevice =
                    document.getElementById(
                        'summary-device'
                    );


                const summaryType =
                    document.getElementById(
                        'summary-type'
                    );


                const summaryDuration =
                    document.getElementById(
                        'summary-duration'
                    );


                const summaryQuantity =
                    document.getElementById(
                        'summary-quantity'
                    );


                const prefixBox =
                    document.getElementById(
                        'prefix-box'
                    );


                const prefixInput =
                    document.getElementById(
                        'username_prefix'
                    );


                const form =
                    document.getElementById(
                        'account-create-form'
                    );


                const submitButton =
                    document.getElementById(
                        'submit-btn'
                    );


                function getAccountType() {

                    const selected =
                        document.querySelector(
                            'input[name="account_type"]:checked'
                        );

                    return selected
                        ? parseInt(
                            selected.value,
                            10
                        )
                        : 1;
                }


                function getDeviceType() {

                    const selected =
                        document.querySelector(
                            'input[name="device_type"]:checked'
                        );

                    return selected
                        ? parseInt(
                            selected.value,
                            10
                        )
                        : 1;
                }


                function getSelectedPlan() {

                    const accountType =
                        getAccountType();


                    const duration =
                        parseInt(
                            durationSelect.value || '0',
                            10
                        );


                    if (!duration) {
                        return null;
                    }


                    const expectedType =
                        accountType === 2
                            ? 'special'
                            : 'normal';


                    return plans.find(
                        function (plan) {

                            return (
                                    plan.type
                                    === expectedType
                                )
                                &&
                                (
                                    parseInt(
                                        plan.duration_months,
                                        10
                                    )
                                    === duration
                                )
                                &&
                                (
                                    plan.is_active
                                    === true
                                );

                        }
                    ) || null;
                }


                function formatPrice(value) {

                    return Number(
                        value || 0
                    ).toLocaleString(
                        'fa-IR'
                    );
                }


                function updateDeviceSummary() {

                    const device =
                        getDeviceType();


                    summaryDevice.textContent =
                        device === 2
                            ? 'iPhone'
                            : 'Android';
                }


                function updatePlan() {

                    const plan =
                        getSelectedPlan();


                    priceBox.style.display =
                        'none';


                    totalPriceBox.style.display =
                        'none';


                    planError.style.display =
                        'none';


                    planIdInput.value =
                        '';


                    if (!durationSelect.value) {

                        summaryDuration.textContent =
                            '-';

                        return;
                    }


                    summaryDuration.textContent =
                        durationSelect.value
                        + ' ماه';


                    if (!plan) {

                        planError.textContent =
                            'برای این نوع اکانت و مدت، پلن فعال وجود ندارد.';

                        planError.style.display =
                            'block';

                        return;
                    }


                    planIdInput.value =
                        plan.id;


                    priceValue.textContent =
                        formatPrice(
                            plan.price
                        );


                    priceBox.style.display =
                        'block';


                    updateTotal();
                }


                function updateTotal() {

                    const plan =
                        getSelectedPlan();


                    if (!plan) {

                        totalPriceBox.style.display =
                            'none';

                        return;
                    }


                    let quantity =
                        parseInt(
                            quantityInput.value || '1',
                            10
                        );


                    if (
                        isNaN(quantity)
                        || quantity < 1
                    ) {
                        quantity = 1;
                    }


                    const total =
                        plan.price
                        * quantity;


                    totalPriceValue.textContent =
                        formatPrice(
                            total
                        );


                    totalPriceBox.style.display =
                        'block';


                    summaryQuantity.textContent =
                        quantity;
                }


                function updateAccountType() {

                    const type =
                        getAccountType();


                    summaryType.textContent =
                        type === 2
                            ? 'ویژه'
                            : 'عادی';


                    updatePlan();
                }


                accountTypeInputs.forEach(
                    function (input) {

                        input.addEventListener(
                            'change',
                            updateAccountType
                        );

                    }
                );


                deviceInputs.forEach(
                    function (input) {

                        input.addEventListener(
                            'change',
                            updateDeviceSummary
                        );

                    }
                );


                durationSelect.addEventListener(
                    'change',
                    updatePlan
                );


                quantityInput.addEventListener(
                    'input',
                    updateTotal
                );


                document
                    .querySelectorAll(
                        'input[name="username_mode"]'
                    )
                    .forEach(
                        function (input) {

                            input.addEventListener(
                                'change',
                                function () {

                                    if (
                                        this.value
                                        === 'prefix'
                                    ) {

                                        prefixBox.style.display =
                                            'block';

                                        prefixInput.required =
                                            true;

                                    } else {

                                        prefixBox.style.display =
                                            'none';

                                        prefixInput.required =
                                            false;

                                    }

                                }
                            );

                        }
                    );


                prefixInput.addEventListener(
                    'input',
                    function () {

                        this.value =
                            this.value.replace(
                                /[^A-Za-z0-9]/g,
                                ''
                            );

                    }
                );


                form.addEventListener(
                    'submit',
                    function (event) {

                        const plan =
                            getSelectedPlan();


                        if (!plan) {

                            event.preventDefault();


                            planError.textContent =
                                'لطفاً یک پلن فعال و دارای قیمت انتخاب کنید.';


                            planError.style.display =
                                'block';


                            durationSelect.focus();


                            return;
                        }


                        if (!planIdInput.value) {

                            event.preventDefault();


                            planError.textContent =
                                'پلن انتخاب‌شده معتبر نیست.';


                            planError.style.display =
                                'block';


                            return;
                        }


                        if (
                            prefixInput.value
                            && !/^[A-Za-z0-9]+$/.test(
                                prefixInput.value
                            )
                        ) {

                            event.preventDefault();


                            alert(
                                'نام پایه فقط باید شامل حروف انگلیسی و اعداد باشد.'
                            );


                            prefixInput.focus();


                            return;
                        }


                        submitButton.disabled =
                            true;


                        submitButton.textContent =
                            'در حال ایجاد...';

                    }
                );


                const initialMode =
                    document.querySelector(
                        'input[name="username_mode"]:checked'
                    );


                if (initialMode) {

                    initialMode.dispatchEvent(
                        new Event('change')
                    );

                }


                updateDeviceSummary();

                updateAccountType();

                updateTotal();

            }
        );

    </script>

@endsection
