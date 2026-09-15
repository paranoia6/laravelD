@extends('layouts.admin')

@section('title', 'جزئیات اکانت')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="h4 mb-1">
                    جزئیات اکانت
                </h1>

                <div class="text-muted">
                    اکانت #{{ $account->id }}
                </div>

            </div>


            <a
                href="{{ route('admin.accounts') }}"
                class="btn btn-outline-secondary"
            >
                بازگشت به لیست
            </a>

        </div>


        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-danger">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        <div class="row g-4">


            {{-- اطلاعات --}}

            <div class="col-lg-8">

                <div class="card shadow-sm">

                    <div class="card-header">
                        اطلاعات اکانت
                    </div>


                    <div class="card-body">

                        <div class="row g-3">


                            <div class="col-md-6">

                                <label class="form-label">
                                    Username
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $account->username }}"
                                    readonly
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    وضعیت
                                </label>

                                <div class="form-control">

                                    @if(
                                        $account->status
                                        === \App\Models\Account::STATUS_ACTIVE
                                    )

                                        <span class="badge bg-success">
                                            فعال
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            مسدود
                                        </span>

                                    @endif

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    نوع اکانت
                                </label>

                                <div class="form-control">

                                    {{ $account->plan?->type === 'special'
                                        ? 'ویژه'
                                        : 'عادی'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    مدت فعلی
                                </label>

                                <div class="form-control">

                                    {{ $account->plan?->duration_months ?? '-' }}
                                    ماه

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    مبلغ پرداخت‌شده
                                </label>

                                <div class="form-control">

                                    {{ number_format(
                                        (int) $account->charged_amount
                                    ) }}

                                    تومان

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Support
                                </label>

                                <div class="form-control">

                                    {{ $account->support?->name ?? '---' }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    نوع دستگاه
                                </label>

                                <div class="form-control">

                                    @if((int) $account->device_type === 1)

                                        اندروید

                                    @elseif((int) $account->device_type === 2)

                                        آیفون

                                    @else

                                        ---

                                    @endif

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Admin
                                </label>

                                <div class="form-control">

                                    {{ $account->admin?->email ?? '---' }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    تاریخ فعال‌سازی
                                </label>

                                <div class="form-control">

                                    {{ $account->activated_at
                                        ? jalali_date(
                                            $account->activated_at,
                                            'Y/m/d H:i:s'
                                        )
                                        : '---'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    اولین ورود
                                </label>

                                <div class="form-control">

                                    {{ $account->first_login_at
                                        ? jalali_date(
                                            $account->first_login_at,
                                            'Y/m/d H:i:s'
                                        )
                                        : 'هنوز وارد نشده'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    تاریخ انقضا
                                </label>

                                <div class="form-control">

                                    {{ $account->expired_at
                                        ? jalali_date(
                                            $account->expired_at,
                                            'Y/m/d H:i:s'
                                        )
                                        : '---'
                                    }}

                                </div>

                            </div>


                            @if(
                                $account->status
                                === \App\Models\Account::STATUS_BLOCKED
                            )

                                <div class="col-md-6">

                                    <label class="form-label">
                                        تاریخ مسدودی
                                    </label>

                                    <div class="form-control">

                                        {{ $account->blocked_at
                                            ? jalali_date(
                                                $account->blocked_at,
                                                'Y/m/d H:i:s'
                                            )
                                            : '---'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        دلیل مسدودی
                                    </label>

                                    <div class="form-control">

                                        @switch($account->block_reason)

                                            @case('blocked_under_72_hours')

                                                مسدودی زیر ۷۲ ساعت

                                                @break

                                            @case('blocked_over_72_hours')

                                                مسدودی پس از ۷۲ ساعت

                                                @break

                                            @default

                                                {{ $account->block_reason ?? '---' }}

                                        @endswitch

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- تمدید --}}

                @if(
                    $account->status
                    === \App\Models\Account::STATUS_ACTIVE
                )

                    <div
                        class="card shadow-sm mt-4"
                        id="renew-account"
                    >

                        <div class="card-header">

                            <strong>
                                تمدید اکانت
                            </strong>

                        </div>


                        <div class="card-body">

                            @if(
                                ! $account->first_login_at
                                || ! $account->expired_at
                            )

                                <div class="alert alert-warning mb-0">

                                    این اکانت هنوز اولین ورود را انجام نداده است؛
                                    بنابراین زمان اعتبار آن هنوز شروع نشده و
                                    امکان تمدید وجود ندارد.

                                </div>

                            @elseif($renewalPlans->isEmpty())

                                <div class="alert alert-warning mb-0">

                                    در حال حاضر پلن فعالی برای تمدید این اکانت
                                    ثبت نشده است.

                                </div>

                            @else

                                @php

                                    $isCurrentlyValid =
                                        $account->expired_at
                                        && $account->expired_at->isFuture();

                                @endphp


                                @if($isCurrentlyValid)

                                    <div class="alert alert-info">

                                        این اکانت هنوز اعتبار دارد.

                                        <br>

                                        تمدید جدید از تاریخ انقضای فعلی اضافه می‌شود:

                                        <strong>
                                            {{ jalali_date(
                                                $account->expired_at,
                                                'Y/m/d H:i'
                                            ) }}
                                        </strong>

                                    </div>

                                @else

                                    <div class="alert alert-warning">

                                        اعتبار این اکانت تمام شده است.

                                        <br>

                                        تمدید جدید از تاریخ امروز محاسبه می‌شود.

                                    </div>

                                @endif


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.accounts.renew',
                                        $account
                                    ) }}"
                                    onsubmit="return confirmRenewal()"
                                >

                                    @csrf


                                    <div class="row g-3 align-items-end">

                                        <div class="col-md-8">

                                            <label
                                                for="renew-plan"
                                                class="form-label"
                                            >
                                                مدت تمدید
                                            </label>

                                            <select
                                                name="plan_id"
                                                id="renew-plan"
                                                class="form-select"
                                                required
                                            >

                                                <option value="">
                                                    انتخاب مدت تمدید
                                                </option>

                                                @foreach(
                                                    $renewalPlans
                                                    as $renewalPlan
                                                )

                                                    <option
                                                        value="{{ $renewalPlan->id }}"
                                                        data-price="{{ (int) $renewalPlan->price }}"
                                                        data-months="{{ (int) $renewalPlan->duration_months }}"
                                                    >

                                                        {{ $renewalPlan->duration_months }}
                                                        ماهه

                                                        —
                                                        {{ number_format(
                                                            (int) $renewalPlan->price
                                                        ) }}
                                                        تومان

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-md-4">

                                            <button
                                                type="submit"
                                                class="btn btn-primary w-100"
                                            >
                                                تمدید اکانت
                                            </button>

                                        </div>

                                    </div>


                                    <div
                                        class="renewal-wallet-info mt-3"
                                    >

                                        مبلغ تمدید از Wallet کسر می‌شود.

                                        @if(
                                            auth()->user()->role->value
                                            === 'admin'
                                        )

                                            موجودی فعلی:

                                            <strong>
                                                {{ number_format(
                                                    (int) auth()->user()->balance
                                                ) }}
                                                تومان
                                            </strong>

                                        @else

                                            <strong>
                                                Super Admin
                                            </strong>

                                            محدودیت موجودی ندارد.

                                        @endif

                                    </div>

                                </form>

                            @endif

                        </div>

                    </div>

                @endif


                {{-- عملیات Block --}}

                @if(
                    $account->status
                    === \App\Models\Account::STATUS_ACTIVE
                )

                    <div class="card shadow-sm mt-4">

                        <div class="card-header">
                            مسدودسازی اکانت
                        </div>


                        <div class="card-body">

                            @php

                                $activatedAt =
                                    $account->activated_at;

                                $hoursPassed =
                                    $activatedAt
                                        ? $activatedAt->diffInHours(now())
                                        : null;

                                $within72 =
                                    $hoursPassed !== null
                                    && $hoursPassed <= 72;

                            @endphp


                            @if($within72)

                                <div class="alert alert-warning">

                                    این اکانت کمتر یا مساوی ۷۲ ساعت پیش
                                    فعال شده است.

                                    <br>

                                    در صورت مسدودسازی،

                                    <strong>
                                        {{ number_format(
                                            (int) $account->charged_amount
                                        ) }}
                                        تومان
                                    </strong>

                                    به موجودی Admin برگشت داده می‌شود.

                                </div>

                            @else

                                <div class="alert alert-danger">

                                    بیش از ۷۲ ساعت از فعال‌سازی اکانت گذشته است.

                                    <br>

                                    در صورت مسدودسازی،
                                    مبلغی برگشت داده نمی‌شود.

                                </div>

                            @endif


                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.accounts.block',
                                    $account
                                ) }}"
                                onsubmit="return confirmBlock()"
                            >

                                @csrf

                                @method('PATCH')


                                <input
                                    type="hidden"
                                    name="confirm_over_72_hours"
                                    value="0"
                                    id="confirm-over-72"
                                >


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    مسدود کردن اکانت
                                </button>

                            </form>

                        </div>

                    </div>

                @endif

            </div>


            {{-- QR --}}

            <div class="col-lg-4">

                <div class="card shadow-sm text-center">

                    <div class="card-header">
                        QR Code
                    </div>


                    <div class="card-body">

                        <div class="bg-white border rounded p-3 d-inline-block">

                            {!! QrCode::size(240)
                                ->margin(1)
                                ->generate(
                                    json_encode(
                                        [
                                            'username' =>
                                                $account->username,

                                            'account_id' =>
                                                $account->id,
                                        ],
                                        JSON_UNESCAPED_UNICODE
                                        | JSON_UNESCAPED_SLASHES
                                    )
                                )
                            !!}

                        </div>


                        <p class="text-muted mt-3 mb-0">
                            QR Code اکانت
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <style>

        #renew-account {
            scroll-margin-top: 100px;
        }

        .renewal-wallet-info {
            background: #f8f9fa;
            border-radius: 9px;
            padding: 12px 14px;
            color: #6b7280;
            font-size: 13px;
        }

    </style>


    <script>

        function confirmRenewal() {

            const select =
                document.getElementById(
                    'renew-plan'
                );

            if (!select || !select.value) {

                alert(
                    'لطفاً مدت تمدید را انتخاب کنید.'
                );

                return false;
            }

            const option =
                select.options[
                    select.selectedIndex
                    ];

            const months =
                option.dataset.months;

            const price =
                Number(
                    option.dataset.price || 0
                );

            return confirm(
                'اکانت برای '
                + months
                + ' ماه تمدید می‌شود و مبلغ '
                + price.toLocaleString('fa-IR')
                + ' تومان از Wallet کسر خواهد شد.'
                + '\n\nآیا ادامه می‌دهید؟'
            );
        }


        function confirmBlock() {

            const within72 =
                {{ $within72 ? 'true' : 'false' }};

            if (within72) {

                return confirm(
                    'این اکانت زیر ۷۲ ساعت فعال شده است. '
                    + 'در صورت مسدودسازی مبلغ به موجودی Admin برگشت داده می‌شود. '
                    + 'آیا ادامه می‌دهید؟'
                );

            }

            const confirmed = confirm(
                'بیش از ۷۲ ساعت از فعال‌سازی گذشته است. '
                + 'هیچ مبلغی برگشت داده نمی‌شود. '
                + 'آیا مطمئن هستید؟'
            );

            if (confirmed) {

                document.getElementById(
                    'confirm-over-72'
                ).value = '1';

                return true;
            }

            return false;
        }

    </script>

@endsection
