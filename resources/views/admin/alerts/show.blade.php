@extends('layouts.admin')

@section('title', 'جزئیات هشدار')
@section('page_title', 'جزئیات هشدار')

@section('content')

    <div class="page-header">
        <div>
            <h1>جزئیات هشدار</h1>
            <p>مشاهده اطلاعات و تنظیمات هشدار</p>
        </div>

        <a href="{{ route('admin.alerts') }}" class="back-button">
            بازگشت به هشدارها
        </a>
    </div>

    <div class="user-details-grid">

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>اطلاعات هشدار</h2>
                    <p>محتوای اصلی پیام</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>شناسه</span>
                    <strong>#{{ $alert->id }}</strong>
                </div>

                <div class="user-info-row">
                    <span>عنوان</span>
                    <strong>{{ $alert->title }}</strong>
                </div>

                <div class="user-info-row">
                    <span>نوع</span>
                    <strong>{{ $alert->type?->value ?? '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>پیام</span>
                    <strong>{{ $alert->message }}</strong>
                </div>

                <div class="user-info-row">
                    <span>متن دکمه</span>
                    <strong>{{ $alert->button_text ?: '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>آدرس دکمه</span>
                    <strong>{{ $alert->button_url ?: '—' }}</strong>
                </div>

            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>شرایط نمایش</h2>
                    <p>محدودیت‌ها و زمان‌بندی هشدار</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>وضعیت</span>
                    <strong>
                        {{ $alert->is_active ? 'فعال' : 'غیرفعال' }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>حداقل Build</span>
                    <strong>{{ $alert->min_build_number ?? '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>حداکثر Build</span>
                    <strong>{{ $alert->max_build_number ?? '—' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>اولویت</span>
                    <strong>{{ $alert->priority }}</strong>
                </div>

                <div class="user-info-row">
                    <span>شروع نمایش</span>
                    <strong>
                        {{ jalali_date($alert->starts_at) }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>پایان نمایش</span>
                    <strong>
                        {{ jalali_date($alert->ends_at) }}
                    </strong>
                </div>

            </div>
        </div>

    </div>

@endsection
