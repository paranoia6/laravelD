@extends('layouts.admin')

@section('title', 'اکانت‌ها')
@section('page_title', 'اکانت‌ها')

@section('content')

    <div class="page-header">

        <div>
            <h1>مدیریت اکانت‌ها</h1>

            <p>
                مشاهده، جستجو و مدیریت اکانت‌های ساخته‌شده
            </p>
        </div>

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


    {{-- Search --}}

    <div class="account-search-card">

        <form
            method="GET"
            action="{{ route('admin.accounts') }}"
            class="account-search-form"
        >

            <div class="account-search-input-wrap">

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    class="account-search-input"
                    placeholder="جستجوی Username..."
                    autocomplete="off"
                >

            </div>

            <button
                type="submit"
                class="account-search-button"
            >
                جستجو
            </button>

            @if(!empty($search))

                <a
                    href="{{ route('admin.accounts') }}"
                    class="account-search-clear"
                >
                    پاک کردن
                </a>

            @endif

        </form>

    </div>


    <div class="account-table-card">

        <div class="account-table-header">

            <div>

                <strong>
                    لیست اکانت‌ها
                </strong>

                <span>
                    {{ $accounts->total() }}
                    اکانت
                </span>

            </div>

            @if(!empty($search))

                <div class="account-search-result-text">
                    نتیجه جستجو برای:
                    <strong>{{ $search }}</strong>
                </div>

            @endif

        </div>


        <div class="table-responsive">

            <table class="admin-table">

                <thead>

                <tr>

                    <th>
                        نام کاربری
                    </th>

                    <th>
                        Admin
                    </th>

                    <th>
                        پلن
                    </th>

                    <th>
                        مبلغ
                    </th>

                    <th>
                        فعال‌سازی
                    </th>

                    <th>
                        انقضا
                    </th>

                    <th>
                        وضعیت
                    </th>

                    <th>
                        عملیات
                    </th>

                </tr>

                </thead>


                <tbody>

                @forelse($accounts as $account)

                    @php

                        $isBlocked =
                            $account->status === 'blocked';

                        $isExpired =
                            ! $isBlocked
                            && $account->expired_at
                            && $account->expired_at->isPast();

                        $hoursSinceFirstLogin =
                            $account->first_login_date
                                ? $account
                                    ->first_login_date
                                    ->diffInHours(now())
                                : null;

                        $canRefund =
                            ! $isBlocked
                            && $hoursSinceFirstLogin !== null
                            && $hoursSinceFirstLogin <= 72;

                        $canRenew =
                            ! $isBlocked
                            && $account->first_login_date
                            && $account->expired_at;

                    @endphp


                    <tr>

                        <td>

                            <a
                                href="{{ route(
                                    'admin.accounts.show',
                                    $account
                                ) }}"
                                class="account-details-link"
                            >

                                <strong>
                                    {{ $account->username }}
                                </strong>

                            </a>

                        </td>


                        <td>
                            {{ $account->admin?->email ?? '-' }}
                        </td>


                        <td>

                            @if($account->plan)

                                {{ $account->plan->type === 'special'
                                    ? 'ویژه'
                                    : 'عادی'
                                }}

                                -

                                {{ $account->plan->duration_months }}
                                ماه

                            @else

                                -

                            @endif

                        </td>


                        <td>

                            {{ number_format(
                                (int) $account->charged_amount
                            ) }}

                            تومان

                        </td>


                        <td>

                            {{ $account->created_at
                                ? jalali_date(
                                    $account->created_at,
                                    'Y/m/d H:i'
                                )
                                : '-'
                            }}

                        </td>


                        <td>

                            {{ $account->expired_at
                                ? jalali_date(
                                    $account->expired_at,
                                    'Y/m/d'
                                )
                                : '-'
                            }}

                        </td>


                        <td>

                            @if($isBlocked)

                                <span class="status-badge blocked">
                                    مسدود
                                </span>

                            @elseif($isExpired)

                                <span class="status-badge expired">
                                    منقضی
                                </span>

                            @else

                                <span class="status-badge active">
                                    فعال
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="account-actions">

                                <a
                                    href="{{ route(
                                        'admin.accounts.show',
                                        $account
                                    ) }}"
                                    class="account-view-button"
                                >
                                    جزئیات
                                </a>


                                @if($canRenew)

                                    <a
                                        href="{{ route(
                                            'admin.accounts.show',
                                            $account
                                        ) }}#renew-account"
                                        class="account-renew-button"
                                    >
                                        تمدید
                                    </a>

                                @endif


                                @if(!$isBlocked)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.accounts.block',
                                            $account
                                        ) }}"
                                        onsubmit="return confirmAccountBlock(
                                            this,
                                            {{ $canRefund ? 'true' : 'false' }}
                                        )"
                                    >

                                        @csrf

                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="confirm_over_72_hours"
                                            value="0"
                                        >

                                        <button
                                            type="submit"
                                            class="account-block-button"
                                        >
                                            مسدود کردن
                                        </button>

                                    </form>

                                @else

                                    <span class="blocked-label">
                                        مسدود شده
                                    </span>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty-table"
                        >

                            @if(!empty($search))
                                اکانتی با این Username پیدا نشد.
                            @else
                                هنوز اکانتی ثبت نشده است.
                            @endif

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        <div class="account-pagination">
            {{ $accounts->links() }}
        </div>

    </div>


    <style>

        .account-search-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .account-search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .account-search-input-wrap {
            flex: 1;
            min-width: 240px;
        }

        .account-search-input {
            width: 100%;
            height: 42px;
            border: 1px solid #ddd;
            border-radius: 9px;
            padding: 0 14px;
            outline: none;
            font-size: 14px;
            background: #fafafa;
        }

        .account-search-input:focus {
            border-color: #d95d91;
            background: #fff;
        }

        .account-search-button {
            border: 0;
            height: 42px;
            padding: 0 20px;
            border-radius: 9px;
            cursor: pointer;
            background: #d95d91;
            color: #fff;
            font-weight: 700;
        }

        .account-search-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 14px;
            border-radius: 9px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .account-search-result-text {
            color: #6b7280;
            font-size: 13px;
        }

        .account-details-link {
            color: inherit;
            text-decoration: none;
        }

        .account-details-link:hover {
            opacity: .7;
            text-decoration: underline;
        }

        .account-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .account-view-button,
        .account-renew-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .account-view-button {
            background: #f3f4f6;
            color: #374151;
        }

        .account-view-button:hover {
            background: #e5e7eb;
        }

        .account-renew-button {
            background: #d95d91;
            color: #fff;
        }

        .account-renew-button:hover {
            background: #c84e82;
            color: #fff;
        }

        .account-block-button {
            border: 0;
            cursor: pointer;
        }

        @media (max-width: 700px) {

            .account-search-input-wrap {
                min-width: 100%;
                flex-basis: 100%;
            }

            .account-search-button,
            .account-search-clear {
                flex: 1;
            }

        }

    </style>


    <script>

        function confirmAccountBlock(
            form,
            under72Hours
        ) {

            if (under72Hours) {

                return confirm(
                    'این اکانت زیر ۷۲ ساعت فعال شده است. '
                    + 'در صورت مسدودسازی مبلغ پرداخت‌شده '
                    + 'به موجودی Admin برگشت داده می‌شود. '
                    + 'آیا ادامه می‌دهید؟'
                );

            }

            const confirmed = confirm(
                'بیش از ۷۲ ساعت از فعال‌سازی این اکانت گذشته است. '
                + 'در صورت مسدودسازی هیچ مبلغی برگشت داده نمی‌شود. '
                + 'آیا مطمئن هستید؟'
            );

            if (confirmed) {

                const input =
                    form.querySelector(
                        'input[name="confirm_over_72_hours"]'
                    );

                if (input) {
                    input.value = '1';
                }

                return true;
            }

            return false;
        }

    </script>

@endsection
