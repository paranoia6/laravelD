@extends('layouts.admin')

@section('title', 'نتیجه ساخت اکانت')

@section('content')

    <div class="account-result-page">

        <div class="account-result-top">

            <div>
                <h1>اکانت‌های ساخته‌شده</h1>
                <p>اطلاعات ورود و QR Code اکانت‌ها</p>
            </div>

            <div class="account-result-actions">

                <a
                    href="{{ route('admin.accounts.create') }}"
                    class="result-btn result-btn-primary"
                >
                    ساخت اکانت جدید
                </a>

                <a
                    href="{{ route('admin.accounts') }}"
                    class="result-btn result-btn-secondary"
                >
                    لیست اکانت‌ها
                </a>

            </div>

        </div>


        @if(empty($createdAccounts))

            <div class="result-empty">
                <div class="result-empty-title">
                    اطلاعاتی برای نمایش وجود ندارد.
                </div>

                <div class="result-empty-text">
                    نتیجه ساخت اکانت در این نشست پیدا نشد.
                </div>
            </div>

        @else

            @if(count($createdAccounts) > 1)

                <div class="result-summary">

                    <div>
                        <strong>{{ count($createdAccounts) }}</strong>
                        اکانت با موفقیت ساخته شد.
                    </div>

                    <button
                        type="button"
                        class="result-btn result-btn-primary"
                        id="copy-all-accounts"
                    >
                        کپی همه اطلاعات
                    </button>

                </div>

            @endif


            <div class="account-result-list">

                @foreach($createdAccounts as $index => $account)

                    <article class="account-result-card">

                        <div class="account-result-card-header">

                            <div class="account-result-number">
                                <span>اکانت</span>
                                <strong>#{{ $account['id'] }}</strong>
                            </div>

                            <span class="result-status">
                            فعال
                        </span>

                        </div>


                        <div class="account-result-card-body">

                            {{-- QR --}}
                            <div class="account-result-qr">

                                <div class="account-result-qr-box">

                                    {!! QrCode::size(190)
                                        ->margin(1)
                                        ->generate(
                                            json_encode(
                                                [
                                                    'username' => $account['username'],
                                                    'password' => $account['password'],
                                                    'account_id' => $account['id'],
                                                ],
                                                JSON_UNESCAPED_UNICODE
                                                | JSON_UNESCAPED_SLASHES
                                            )
                                        )
                                    !!}

                                </div>

                                <div class="account-result-qr-title">
                                    QR ورود اکانت
                                </div>

                                <div class="account-result-qr-hint">
                                    برای ورود سریع اسکن کنید
                                </div>

                            </div>


                            {{-- Information --}}
                            <div class="account-result-info">

                                <div class="account-copy-section">

                                    <div class="account-field-title">
                                        Username
                                    </div>

                                    <div class="account-copy-row">

                                        <div class="account-copy-value">
                                            {{ $account['username'] }}
                                        </div>

                                        <button
                                            type="button"
                                            class="account-copy-button"
                                            data-copy="{{ $account['username'] }}"
                                        >
                                            کپی
                                        </button>

                                    </div>

                                </div>


                                <div class="account-copy-section">

                                    <div class="account-field-title">
                                        Password
                                    </div>

                                    <div class="account-copy-row">

                                        <div class="account-copy-value">
                                            {{ $account['password'] }}
                                        </div>

                                        <button
                                            type="button"
                                            class="account-copy-button"
                                            data-copy="{{ $account['password'] }}"
                                        >
                                            کپی
                                        </button>

                                    </div>

                                </div>


                                <div class="account-meta-grid">

                                    <div class="account-meta-item">
                                        <span>نوع اکانت</span>

                                        <strong>
                                            {{ (int) $account['account_type'] === 1
                                                ? 'عادی'
                                                : 'ویژه'
                                            }}
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>دستگاه</span>

                                        <strong>
                                            {{ (int) $account['device_type'] === 1
                                                ? 'اندروید'
                                                : 'آیفون'
                                            }}
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>مدت</span>

                                        <strong>
                                            {{ $account['duration_months'] }}
                                            ماه
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>مبلغ</span>

                                        <strong>
                                            {{ number_format((int) $account['plan_price']) }}
                                            تومان
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>پشتیبانی</span>

                                        <strong>
                                            {{ $account['support'] ?? '---' }}
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>تاریخ فعال‌سازی</span>

                                        <strong>
                                            {{ $account['activated_at'] ?? '---' }}
                                        </strong>
                                    </div>


                                    <div class="account-meta-item">
                                        <span>تاریخ انقضا</span>

                                        <strong>
                                            {{ $account['expired_at'] ?? '---' }}
                                        </strong>
                                    </div>

                                </div>


                                <div class="account-result-footer">

                                    <a
                                        href="{{ route(
                                        'admin.accounts.show',
                                        $account['id']
                                    ) }}"
                                        class="result-btn result-btn-primary"
                                    >
                                        مشاهده اکانت
                                    </a>

                                    <button
                                        type="button"
                                        class="result-btn result-btn-secondary copy-account-button"
                                        data-index="{{ $index }}"
                                    >
                                        کپی اطلاعات این اکانت
                                    </button>

                                </div>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            async function copyText(value, button = null) {

                try {

                    await navigator.clipboard.writeText(value);

                    if (button) {

                        const oldText = button.innerText;

                        button.innerText = 'کپی شد';

                        setTimeout(function () {
                            button.innerText = oldText;
                        }, 1200);

                    }

                } catch (error) {

                    alert('کپی کردن انجام نشد.');

                }

            }


            document
                .querySelectorAll('.account-copy-button')
                .forEach(function (button) {

                    button.addEventListener('click', function () {

                        copyText(
                            this.dataset.copy,
                            this
                        );

                    });

                });


            const accounts = @json($createdAccounts);


            document
                .querySelectorAll('.copy-account-button')
                .forEach(function (button) {

                    button.addEventListener('click', function () {

                        const index = Number(this.dataset.index);
                        const account = accounts[index];

                        if (!account) {
                            return;
                        }

                        const text =
                            'Account #' + account.id + '\n' +
                            'Username: ' + account.username + '\n' +
                            'Password: ' + account.password + '\n' +
                            'نوع اکانت: ' +
                            ((Number(account.account_type) === 1)
                                ? 'عادی'
                                : 'ویژه') + '\n' +
                            'دستگاه: ' +
                            ((Number(account.device_type) === 1)
                                ? 'اندروید'
                                : 'آیفون') + '\n' +
                            'مدت: ' +
                            account.duration_months +
                            ' ماه\n' +
                            'مبلغ: ' +
                            Number(account.plan_price).toLocaleString('en-US') +
                            ' تومان\n' +
                            'پشتیبانی: ' +
                            (account.support || '---') + '\n' +
                            'فعال‌سازی: ' +
                            (account.activated_at || '---') + '\n' +
                            'انقضا: ' +
                            (account.expired_at || '---');

                        copyText(text, this);

                    });

                });


            const copyAllButton =
                document.getElementById('copy-all-accounts');


            if (copyAllButton) {

                copyAllButton.addEventListener('click', function () {

                    const text = accounts
                        .map(function (account) {

                            return (
                                'Account #' + account.id + '\n' +
                                'Username: ' + account.username + '\n' +
                                'Password: ' + account.password + '\n' +
                                'نوع اکانت: ' +
                                ((Number(account.account_type) === 1)
                                    ? 'عادی'
                                    : 'ویژه') + '\n' +
                                'دستگاه: ' +
                                ((Number(account.device_type) === 1)
                                    ? 'اندروید'
                                    : 'آیفون') + '\n' +
                                'مدت: ' +
                                account.duration_months +
                                ' ماه\n' +
                                'مبلغ: ' +
                                Number(account.plan_price).toLocaleString('en-US') +
                                ' تومان\n' +
                                'پشتیبانی: ' +
                                (account.support || '---') + '\n' +
                                'فعال‌سازی: ' +
                                (account.activated_at || '---') + '\n' +
                                'انقضا: ' +
                                (account.expired_at || '---')
                            );

                        })
                        .join('\n\n--------------------\n\n');

                    copyText(text, this);

                });

            }

        });

    </script>

@endsection
