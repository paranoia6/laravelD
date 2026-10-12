@extends('layouts.admin')

@section('title', 'داشبورد')

@section('content')

    @php
        $user = auth()->user();
        $isSuperAdmin = $user?->role?->value === 'super_admin';

        $jalali = function ($date, $withTime = true) {
            if (!$date) {
                return '—';
            }

            return \Morilog\Jalali\Jalalian::fromDateTime($date)
                ->format($withTime ? 'Y/m/d H:i' : 'Y/m/d');
        };
    @endphp

    <div class="dashboard-page">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="dashboard-header">
            <div>
                <h1>داشبورد</h1>
                <p>
                    {{ $isSuperAdmin
                        ? 'نمای کلی عملکرد پنل و Adminها'
                        : 'نمای کلی عملکرد و وضعیت اکانت‌های شما'
                    }}
                </p>
            </div>

            <div class="dashboard-header-actions">
                <a href="{{ route('admin.accounts.create') }}" class="dashboard-action primary">
                    + ایجاد اکانت
                </a>

                <a href="{{ route('admin.reports') }}" class="dashboard-action secondary">
                    گزارش‌ها
                </a>
            </div>
        </div>


        {{-- =========================================================
            LOW BALANCE
        ========================================================== --}}
        @if(!$isSuperAdmin && (int) $user->balance < 100000)

            <div class="dashboard-low-balance">
                <div>
                    <strong>هشدار موجودی</strong>
                    <span>
                    موجودی کیف پول شما کمتر از ۱۰۰,۰۰۰ تومان است.
                </span>
                </div>

                <a href="{{ route('admin.wallet') }}">
                    مشاهده کیف پول
                </a>
            </div>

        @endif


        {{-- =========================================================
            STATISTICS
        ========================================================== --}}
        <div class="dashboard-grid">

            <div class="dashboard-card">
                <div class="dashboard-card-title">
                    <span>کل اکانت‌ها</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['total_accounts'] ?? 0)) }}
                </strong>

                <small>تمام اکانت‌های ساخته‌شده</small>
            </div>


            <div class="dashboard-card">
                <div class="dashboard-card-title">
                    <span>فعال‌شده</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['activated_accounts'] ?? $stats['active_accounts'] ?? 0)) }}
                </strong>

                <small>حداقل یک ورود موفق</small>
            </div>


            <div class="dashboard-card">
                <div class="dashboard-card-title">
                    <span>در انتظار فعال‌سازی</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['not_activated_accounts'] ?? 0)) }}
                </strong>

                <small>هنوز اولین ورود انجام نشده</small>
            </div>


            <div class="dashboard-card">
                <div class="dashboard-card-title">
                    <span>ساخته‌شده امروز</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['today_accounts'] ?? 0)) }}
                </strong>

                <small>اکانت‌های امروز</small>
            </div>


            <div class="dashboard-card">
                <div class="dashboard-card-title">
                    <span>ساخته‌شده این ماه</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['month_accounts'] ?? 0)) }}
                </strong>

                <small>اکانت‌های ماه جاری</small>
            </div>


            <div class="dashboard-card warning">
                <div class="dashboard-card-title">
                    <span>در حال انقضا</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['expiring_accounts'] ?? 0)) }}
                </strong>

                <small>حداکثر ۷ روز تا انقضا</small>
            </div>


            <div class="dashboard-card danger">
                <div class="dashboard-card-title">
                    <span>مسدود زیر ۷۲ ساعت</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['blocked_under_72_hours'] ?? 0)) }}
                </strong>

                <small>مشمول بررسی بازگشت وجه</small>
            </div>


            <div class="dashboard-card danger">
                <div class="dashboard-card-title">
                    <span>مسدود بالای ۷۲ ساعت</span>
                </div>

                <strong>
                    {{ number_format((int) ($stats['blocked_over_72_hours'] ?? 0)) }}
                </strong>

                <small>بدون بازگشت خودکار وجه</small>
            </div>


            {{-- =====================================================
                SALES - SUPER ADMIN ONLY
            ====================================================== --}}
            @if(auth()->user()->role?->value === 'super_admin')
                <div class="dashboard-card">

                    <div class="dashboard-card-title">
                        فروش کل
                    </div>

                    <div class="dashboard-card-value">
                        {{ number_format($stats['totalSales'] ?? 0) }}
                    </div>

                    <div class="dashboard-card-meta">
                        تومان
                    </div>

                </div>
            @endif

        </div>


        {{-- =========================================================
            SUPER ADMIN ADMIN SUMMARY
        ========================================================== --}}
        @if($isSuperAdmin && isset($adminStats))

            <section class="dashboard-section">

                <div class="dashboard-section-header">
                    <div>
                        <h2>عملکرد Adminها</h2>
                        <p>وضعیت اکانت‌های ساخته‌شده توسط هر Admin</p>
                    </div>

                    <a href="{{ route('admin.admins') }}">
                        مدیریت Adminها
                    </a>
                </div>


                <div class="dashboard-admin-grid">

                    @forelse($adminStats as $admin)

                        <div class="dashboard-admin-card">

                            <div class="dashboard-admin-top">

                                <div class="dashboard-admin-avatar">
                                    {{ mb_strtoupper(mb_substr($admin->email ?? 'A', 0, 1)) }}
                                </div>

                                <div class="dashboard-admin-info">
                                    <strong>{{ $admin->email }}</strong>

                                    @if($admin->is_active)
                                        <span class="dashboard-status active">
                                        فعال
                                    </span>
                                    @else
                                        <span class="dashboard-status blocked">
                                        غیرفعال
                                    </span>
                                    @endif
                                </div>

                            </div>


                            <div class="dashboard-admin-stats">

                                <div>
                                    <span>کل</span>
                                    <strong>
                                        {{ number_format((int) $admin->total_accounts) }}
                                    </strong>
                                </div>

                                <div>
                                    <span>فعال‌شده</span>
                                    <strong>
                                        {{ number_format((int) $admin->activated_accounts) }}
                                    </strong>
                                </div>

                                <div>
                                    <span>در انتظار</span>
                                    <strong>
                                        {{ number_format((int) $admin->not_activated_accounts) }}
                                    </strong>
                                </div>

                                <div>
                                    <span>مسدود</span>
                                    <strong>
                                        {{ number_format((int) $admin->blocked_accounts) }}
                                    </strong>
                                </div>

                                <div>
                                    <span>در حال انقضا</span>
                                    <strong>
                                        {{ number_format((int) $admin->expiring_accounts) }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="dashboard-empty">
                            هنوز Adminی ثبت نشده است.
                        </div>

                    @endforelse

                </div>

            </section>

        @endif


        {{-- =========================================================
            RECENT ACCOUNTS
        ========================================================== --}}
        <section class="dashboard-section">

            <div class="dashboard-section-header">
                <div>
                    <h2>آخرین اکانت‌ها</h2>
                    <p>
                        آخرین اکانت‌هایی که در سیستم ساخته شده‌اند
                    </p>
                </div>

                <a href="{{ route('admin.accounts') }}">
                    مشاهده همه
                </a>
            </div>


            <div class="dashboard-table-card">

                @if($recentAccounts->count())

                    <div class="dashboard-table-wrapper">

                        <table class="dashboard-table">

                            <thead>
                            <tr>

                                <th>#</th>

                                @if($isSuperAdmin)
                                    <th>Admin</th>
                                @endif

                                <th>Username</th>
                                <th>پلن</th>
                                <th>نوع</th>
                                <th>وضعیت</th>
                                <th>اولین ورود</th>
                                <th>تاریخ ساخت</th>

                            </tr>
                            </thead>

                            <tbody>

                            @foreach($recentAccounts as $account)

                                <tr>

                                    <td>
                                        #{{ $account->id }}
                                    </td>


                                    @if($isSuperAdmin)

                                        <td>
                                            {{ $account->admin?->email ?? '—' }}
                                        </td>

                                    @endif


                                    <td>
                                        <strong>
                                            {{ $account->username }}
                                        </strong>
                                    </td>


                                    <td>
                                        @if($account->plan)
                                            {{ $account->plan->duration_months }} ماه
                                        @else
                                            —
                                        @endif
                                    </td>


                                    <td>
                                        @if((int) $account->plan?->type === 2 || $account->plan?->type === 'special')
                                            <span class="dashboard-type special">
                                            ویژه
                                        </span>
                                        @else
                                            <span class="dashboard-type normal">
                                            عادی
                                        </span>
                                        @endif
                                    </td>


                                    <td>

                                        @if($account->status === 'blocked')

                                            <span class="dashboard-status blocked">
                                            مسدود
                                        </span>

                                        @elseif($account->first_login_at && $account->expired_at && $account->expired_at->isPast())

                                            <span class="dashboard-status expired">
                                            منقضی
                                        </span>

                                        @elseif($account->first_login_at)

                                            <span class="dashboard-status active">
                                            فعال
                                        </span>

                                        @else

                                            <span class="dashboard-status pending">
                                            فعال‌نشده
                                        </span>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $jalali($account->first_login_at) }}
                                    </td>


                                    <td>
                                        {{ $jalali($account->created_at) }}
                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="dashboard-empty">
                        هنوز اکانتی ساخته نشده است.
                    </div>

                @endif

            </div>

        </section>

    </div>


    <style>

        .dashboard-page {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 4px 0 35px;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .dashboard-header h1 {
            margin: 0;
            color: #2f252b;
            font-size: 25px;
            font-weight: 900;
        }

        .dashboard-header p {
            margin: 6px 0 0;
            color: #91818a;
            font-size: 12px;
        }

        .dashboard-header-actions {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
        }

        .dashboard-action {
            min-height: 40px;
            padding: 0 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            transition: .2s ease;
        }

        .dashboard-action.primary {
            color: #fff;
            background: #c64d7c;
        }

        .dashboard-action.primary:hover {
            background: #b53e6c;
        }

        .dashboard-action.secondary {
            color: #9b476c;
            background: #fff;
            border: 1px solid #ead9e1;
        }

        .dashboard-action.secondary:hover {
            background: #fff7fa;
        }

        .dashboard-low-balance {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
            padding: 14px 16px;
            border: 1px solid #f0cdd7;
            border-radius: 14px;
            background: #fff3f5;
        }

        .dashboard-low-balance strong {
            display: block;
            margin-bottom: 3px;
            color: #b43c58;
            font-size: 12px;
        }

        .dashboard-low-balance span {
            color: #8c6570;
            font-size: 11px;
        }

        .dashboard-low-balance a {
            color: #b43c58;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            width: 100%;
        }

        .dashboard-card {
            position: relative;
            overflow: hidden;
            min-height: 128px;
            padding: 18px 19px;
            border: 1px solid #eadde4;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(100, 50, 75, .045);
        }

        .dashboard-card::after {
            content: "";
            position: absolute;
            right: 0;
            top: 14px;
            bottom: 14px;
            width: 4px;
            background: #d95d91;
            border-radius: 5px 0 0 5px;
        }

        .dashboard-card.warning::after {
            background: #d49a35;
        }

        .dashboard-card.danger::after {
            background: #d65368;
        }

        .dashboard-card-title {
            margin-bottom: 11px;
            color: #8d7a84;
            font-size: 11px;
            font-weight: 800;
        }

        .dashboard-card > strong {
            display: block;
            color: #34282f;
            font-size: 27px;
            line-height: 1.1;
            font-weight: 900;
        }

        .dashboard-card > strong small {
            color: #927e88;
            font-size: 10px;
            font-weight: 700;
        }

        .dashboard-card > small {
            display: block;
            margin-top: 9px;
            color: #a18f97;
            font-size: 9px;
        }

        .dashboard-sales-card {
            background: linear-gradient(135deg, #fff, #fff8fb);
        }

        .dashboard-sales-card > strong {
            color: #b84372;
        }

        .dashboard-section {
            margin-top: 22px;
        }

        .dashboard-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 12px;
        }

        .dashboard-section-header h2 {
            margin: 0;
            color: #34282f;
            font-size: 15px;
            font-weight: 900;
        }

        .dashboard-section-header p {
            margin: 5px 0 0;
            color: #96858d;
            font-size: 10px;
        }

        .dashboard-section-header > a {
            color: #b44773;
            text-decoration: none;
            font-size: 10px;
            font-weight: 800;
        }

        .dashboard-admin-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .dashboard-admin-card {
            padding: 16px;
            border: 1px solid #eadde4;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(100, 50, 75, .04);
        }

        .dashboard-admin-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .dashboard-admin-avatar {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #fff0f6;
            color: #b54877;
            font-size: 13px;
            font-weight: 900;
        }

        .dashboard-admin-info {
            min-width: 0;
        }

        .dashboard-admin-info strong {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #3b2d34;
            font-size: 11px;
        }

        .dashboard-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 4px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 800;
        }

        .dashboard-status.active {
            color: #16804a;
            background: #eaf9f0;
        }

        .dashboard-status.blocked {
            color: #b63d55;
            background: #fff0f3;
        }

        .dashboard-status.expired {
            color: #a66c00;
            background: #fff6df;
        }

        .dashboard-status.pending {
            color: #8b6173;
            background: #fff3f7;
        }

        .dashboard-admin-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 7px;
        }

        .dashboard-admin-stats > div {
            min-width: 0;
            padding: 8px 5px;
            text-align: center;
            border: 1px solid #f1e5ea;
            border-radius: 9px;
            background: #fffafd;
        }

        .dashboard-admin-stats span {
            display: block;
            color: #9b8991;
            font-size: 8px;
        }

        .dashboard-admin-stats strong {
            display: block;
            margin-top: 4px;
            color: #493840;
            font-size: 13px;
            font-weight: 900;
        }

        .dashboard-table-card {
            overflow: hidden;
            border: 1px solid #eadde4;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(100, 50, 75, .045);
        }

        .dashboard-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        .dashboard-table th {
            padding: 12px 14px;
            color: #8d7b84;
            background: #fffafd;
            border-bottom: 1px solid #f0e4e9;
            font-size: 9px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dashboard-table td {
            padding: 13px 14px;
            color: #685761;
            border-bottom: 1px solid #f5edf1;
            font-size: 9px;
            white-space: nowrap;
        }

        .dashboard-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .dashboard-table tbody tr:hover td {
            background: #fffafd;
        }

        .dashboard-table td strong {
            color: #382b32;
            font-weight: 900;
        }

        .dashboard-type {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 800;
        }

        .dashboard-type.normal {
            color: #61717a;
            background: #f1f5f7;
        }

        .dashboard-type.special {
            color: #a14671;
            background: #fff0f6;
        }

        .dashboard-empty {
            padding: 35px 20px;
            text-align: center;
            color: #9d8b93;
            font-size: 11px;
        }

        @media (max-width: 1200px) {

            .dashboard-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-admin-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        @media (max-width: 850px) {

            .dashboard-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-admin-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 600px) {

            .dashboard-page {
                padding-bottom: 25px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-low-balance {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-section-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-admin-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

        }

    </style>

@endsection
