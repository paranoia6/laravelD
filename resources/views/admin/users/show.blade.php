@extends('layouts.admin')

@section('title', 'جزئیات کاربر')
@section('page_title', 'جزئیات کاربر')

@section('content')

    <div class="page-header">
        <div>
            <h1>جزئیات کاربر</h1>
            <p>اطلاعات حساب و وضعیت کاربر</p>
        </div>

        <div class="user-header-actions">

            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                @csrf
                @method('PATCH')

                <button type="submit"
                        class="user-action-button {{ $user->is_active ? 'danger' : 'success' }}">
                    {{ $user->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
                </button>
            </form>

            <a href="{{ route('admin.users') }}" class="back-button">
                بازگشت به کاربران
            </a>

        </div>
        @if(session('success'))
            <div class="action-success">
                {{ session('success') }}
            </div>
        @endif

    </div>

    <div class="user-details-grid">

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>اطلاعات حساب</h2>
                    <p>اطلاعات اصلی حساب کاربر</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>شناسه کاربر</span>
                    <strong>#{{ $user->id }}</strong>
                </div>

                <div class="user-info-row">
                    <span>ایمیل</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="user-info-row">
                    <span>وضعیت</span>
                    <strong>
                        {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>نوع حساب</span>
                    <strong>
                        {{ $user->is_test ? 'تستی' : 'عادی' }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>تاریخ ثبت</span>
                    <strong>
                        {{ jalali_date($user->created_at) }}
                    </strong>
                </div>

            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>دستگاه و اپلیکیشن</h2>
                    <p>اطلاعات دستگاه آخرین ورود</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>مدل دستگاه</span>
                    <strong>{{ $user->device_model ?: '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>سازنده</span>
                    <strong>{{ $user->manufacturer ?: '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>سیستم‌عامل</span>
                    <strong>{{ $user->os_version ?: '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>نسخه اپ</span>
                    <strong>{{ $user->app_version_code ?: '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>آخرین فعالیت</span>
                    <strong>
                        {{ jalali_date($user->last_seen) }}
                    </strong>
                </div>

            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>اشتراک و دسترسی</h2>
                    <p>وضعیت اعتبار حساب</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>نوع انقضا</span>
                    <strong>{{ $user->expired_type }}</strong>
                </div>

                <div class="user-info-row">
                    <span>تاریخ انقضا</span>
                    <strong>
                        {{ jalali_date($user->expired_at) }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>وضعیت تعلیق</span>

                    <strong>
                        @if($user->suspend_at)
                            تا {{ jalali_date($user->suspend_at) }}
                        @else
                            بدون تعلیق
                        @endif
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>ورود اولیه</span>
                    <strong>
                        {{ jalali_date($user->first_login_date) }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>تلاش ورود</span>
                    <strong>{{ $user->try_login }}</strong>
                </div>

                <div class="user-info-row">
                    <span>اجازه دستگاه دیگر</span>
                    <strong>
                        {{ $user->is_other_device_allow ? 'بله' : 'خیر' }}
                    </strong>
                </div>

            </div>
        </div>

    </div>

@endsection
