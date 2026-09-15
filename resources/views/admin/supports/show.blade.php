@extends('layouts.admin')

@section('title', 'جزئیات تیکت')
@section('page_title', 'جزئیات تیکت')

@section('content')

    <div class="page-header">
        <div>
            <h1>جزئیات درخواست</h1>
            <p>مشاهده اطلاعات درخواست پشتیبانی</p>
        </div>

        <a href="{{ route('admin.supports') }}" class="back-button">
            بازگشت به تیکت‌ها
        </a>
    </div>

    <div class="user-details-grid">

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>اطلاعات درخواست</h2>
                    <p>اطلاعات اصلی درخواست پشتیبانی</p>
                </div>
            </div>

            <div class="user-info-list">

                <div class="user-info-row">
                    <span>شناسه</span>
                    <strong>#{{ $support->id }}</strong>
                </div>

                <div class="user-info-row">
                    <span>عنوان</span>
                    <strong>{{ $support->name ?: 'بدون عنوان' }}</strong>
                </div>

                <div class="user-info-row">
                    <span>شناسه کاربر</span>
                    <strong>
                        {{ $support->user_id ? '#' . $support->user_id : '—' }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>تاریخ ثبت</span>
                    <strong>
                        {{ jalali_date($support->created_at) }}
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>آخرین بروزرسانی</span>
                    <strong>
                        {{ jalali_date($support->updated_at) }}
                    </strong>
                </div>

            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>اطلاعات درخواست</h2>
                    <p>داده‌های ارسال‌شده توسط کاربر</p>
                </div>
            </div>

            @if($support->meta_data)
                <div class="support-meta-data">
                    @foreach($support->meta_data as $key => $value)
                        <div class="user-info-row">
                            <span>{{ $key }}</span>

                            <strong>
                                @if(is_array($value))
                                    {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $value ?: '—' }}
                                @endif
                            </strong>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="support-empty-state">
                    <div class="support-empty-icon">—</div>

                    <strong>اطلاعاتی ثبت نشده است</strong>

                    <span>
                    برای این درخواست داده اضافی ثبت نشده است.
                </span>
                </div>
            @endif

        </div>

    </div>

@endsection
