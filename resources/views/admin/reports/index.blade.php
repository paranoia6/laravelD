@extends('layouts.admin')

@section('title', 'گزارش‌ها')

@section('content')

    <div class="reports-page">

        <div class="reports-header">

            <div>
                <h1>گزارش‌ها</h1>

                <p>
                    گزارش عملکرد، ساخت اکانت و وضعیت چرخه اکانت‌ها
                </p>
            </div>

        </div>


        {{-- خلاصه عملکرد --}}
        <section class="report-section">

            <div class="report-section-title">
                <div>
                    <h2>خلاصه عملکرد</h2>
                    <span>نمای کلی از عملکرد اکانت‌ها</span>
                </div>
            </div>


            <div class="report-summary-grid">

                <div class="report-summary-card">
                    <span>کل اکانت‌های ایجادشده</span>
                    <strong>{{ $stats['total'] }}</strong>
                </div>

                <div class="report-summary-card">
                    <span>ایجاد امروز</span>
                    <strong>{{ $stats['today_created'] }}</strong>
                </div>

                <div class="report-summary-card">
                    <span>ایجاد این ماه</span>
                    <strong>{{ $stats['month_created'] }}</strong>
                </div>

                <div class="report-summary-card">
                    <span>فعال‌شده</span>
                    <strong>{{ $stats['activated'] }}</strong>
                </div>

            </div>

        </section>


        {{-- وضعیت چرخه --}}
        <section class="report-section">

            <div class="report-section-title">

                <div>
                    <h2>وضعیت چرخه اکانت‌ها</h2>
                    <span>وضعیت ورود، انقضا و مسدودی</span>
                </div>

            </div>


            <div class="report-status-grid">

                <div class="report-status-card">

                    <div class="report-status-label">
                        هنوز وارد نشده
                    </div>

                    <strong>
                        {{ $stats['not_activated'] }}
                    </strong>

                    <div class="report-status-description">
                        اکانت‌هایی که هنوز اولین ورود موفق را نداشته‌اند.
                    </div>

                </div>


                <div class="report-status-card">

                    <div class="report-status-label">
                        در حال انقضا
                    </div>

                    <strong>
                        {{ $stats['expiring'] }}
                    </strong>

                    <div class="report-status-description">
                        اکانت‌هایی که در بازه نزدیک به انقضا هستند.
                    </div>

                </div>


                <div class="report-status-card">

                    <div class="report-status-label">
                        منقضی‌شده
                    </div>

                    <strong>
                        {{ $stats['expired'] }}
                    </strong>

                    <div class="report-status-description">
                        اکانت‌هایی که تاریخ انقضای آن‌ها گذشته است.
                    </div>

                </div>


                <div class="report-status-card">

                    <div class="report-status-label">
                        مسدودشده
                    </div>

                    <strong>
                        {{ $stats['blocked'] }}
                    </strong>

                    <div class="report-status-description">
                        مجموع اکانت‌های مسدودشده.
                    </div>

                </div>

            </div>

        </section>


        {{-- گزارش 72 ساعت --}}
        <section class="report-section">

            <div class="report-section-title">

                <div>
                    <h2>گزارش مسدودی</h2>
                    <span>تفکیک مسدودی بر اساس قانون ۷۲ ساعت</span>
                </div>

            </div>


            <div class="block-report-grid">

                <div class="block-report-card block-report-warning">

                    <div class="block-report-top">
                        <span>زیر ۷۲ ساعت</span>
                        <span class="block-report-badge">
                        استرداد
                    </span>
                    </div>

                    <strong>
                        {{ $stats['blocked_under_72'] }}
                    </strong>

                    <p>
                        مسدودی‌هایی که در محدوده ۷۲ ساعت اول قرار گرفته‌اند.
                    </p>

                </div>


                <div class="block-report-card block-report-danger">

                    <div class="block-report-top">
                        <span>بیش از ۷۲ ساعت</span>
                        <span class="block-report-badge">
                        بدون استرداد
                    </span>
                    </div>

                    <strong>
                        {{ $stats['blocked_over_72'] }}
                    </strong>

                    <p>
                        مسدودی‌هایی که بعد از پایان مهلت ۷۲ ساعت انجام شده‌اند.
                    </p>

                </div>

            </div>

        </section>


        @if(auth()->user()->role->value === 'super_admin')

            {{-- عملکرد Admin ها --}}
            <section class="report-section">

                <div class="report-section-title">

                    <div>
                        <h2>گزارش عملکرد Adminها</h2>
                        <span>
                        مقایسه عملکرد و وضعیت اکانت‌های هر Admin
                    </span>
                    </div>

                </div>


                <div class="report-table-card">

                    <div class="report-table-wrap">

                        <table class="report-table">

                            <thead>
                            <tr>
                                <th>Admin</th>
                                <th>موجودی</th>
                                <th>کل اکانت</th>
                                <th>فعال‌شده</th>
                                <th>فعال‌نشده</th>
                                <th>در حال انقضا</th>
                                <th>مسدود</th>
                                <th>وضعیت</th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($admins as $admin)

                                <tr>

                                    <td>
                                        <div class="report-admin-name">
                                            {{ $admin->email }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ number_format((int) $admin->balance) }}
                                        تومان
                                    </td>

                                    <td>
                                        {{ $admin->total_accounts }}
                                    </td>

                                    <td>
                                        {{ $admin->activated_accounts }}
                                    </td>

                                    <td>
                                        {{ $admin->not_activated_accounts }}
                                    </td>

                                    <td>
                                        {{ $admin->expiring_accounts }}
                                    </td>

                                    <td>
                                        {{ $admin->blocked_accounts }}
                                    </td>

                                    <td>

                                        @if($admin->is_active)

                                            <span class="report-badge report-badge-success">
                                            فعال
                                        </span>

                                        @else

                                            <span class="report-badge report-badge-danger">
                                            مسدود
                                        </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="report-empty-row">
                                        Adminای برای نمایش وجود ندارد.
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

        @endif


        {{-- آخرین اکانت‌ها --}}
        <section class="report-section">

            <div class="report-section-title">

                <div>
                    <h2>آخرین اکانت‌های ایجادشده</h2>

                    <span>
                    آخرین فعالیت‌های ثبت‌شده در سیستم
                </span>
                </div>

                <a
                    href="{{ route('admin.accounts') }}"
                    class="report-link"
                >
                    مشاهده همه اکانت‌ها
                </a>

            </div>


            <div class="report-table-card">

                <div class="report-table-wrap">

                    <table class="report-table">

                        <thead>
                        <tr>
                            <th>Username</th>

                            @if(auth()->user()->role->value === 'super_admin')
                                <th>Admin</th>
                            @endif

                            <th>مدت</th>
                            <th>وضعیت</th>
                            <th>اولین ورود</th>
                            <th>انقضا</th>
                        </tr>
                        </thead>


                        <tbody>

                        @forelse($recentAccounts as $account)

                            <tr>

                                <td>
                                <span class="report-username">
                                    {{ $account->username }}
                                </span>
                                </td>


                                @if(auth()->user()->role->value === 'super_admin')

                                    <td>
                                        {{ $account->admin->email ?? '-' }}
                                    </td>

                                @endif


                                <td>
                                    {{ $account->plan->duration_months ?? '-' }}
                                    ماه
                                </td>


                                <td>

                                    @if($account->status === 'blocked')

                                        <span class="report-badge report-badge-danger">
                                        مسدود
                                    </span>

                                    @elseif(!$account->first_login_at)

                                        <span class="report-badge report-badge-warning">
                                        فعال‌نشده
                                    </span>

                                    @else

                                        <span class="report-badge report-badge-success">
                                        فعال
                                    </span>

                                    @endif

                                </td>


                                <td>
                                    {{ jalali_date($account->first_login_at) }}
                                </td>


                                <td>
                                    {{ jalali_date($account->expired_at) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="{{ auth()->user()->role->value === 'super_admin' ? 6 : 5 }}"
                                    class="report-empty-row"
                                >
                                    هنوز اکانتی برای گزارش وجود ندارد.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </div>

@endsection
