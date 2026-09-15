@extends('layouts.admin')

@section('title', 'کاربران')
@section('page_title', 'کاربران')

@section('content')

    <div class="page-header">
        <div>
            <h1>کاربران</h1>
            <p>مدیریت کاربران ثبت‌شده در سیستم SH</p>
        </div>
    </div>

    <div class="dashboard-section users-section">

        <div class="section-heading">
            <div>
                <h2>لیست کاربران</h2>
                <p>{{ number_format($users->total()) }} کاربر ثبت شده است</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.users') }}" class="users-filters">

            <div class="filter-search">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="جستجو با ایمیل..."
                >
            </div>

            <select name="status">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" @selected(request('status') === 'active')>
                    فعال
                </option>
                <option value="inactive" @selected(request('status') === 'inactive')>
                    غیرفعال
                </option>
            </select>

            <select name="type">
                <option value="">همه کاربران</option>
                <option value="normal" @selected(request('type') === 'normal')>
                    عادی
                </option>
                <option value="test" @selected(request('type') === 'test')>
                    تستی
                </option>
            </select>

            <button type="submit" class="filter-button">
                جستجو
            </button>

            @if(request()->hasAny(['search', 'status', 'type']))
                <a href="{{ route('admin.users') }}" class="filter-reset">
                    پاک کردن
                </a>
            @endif

        </form>
        @if($users->count())
            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>ایمیل</th>
                        <th>وضعیت</th>
                        <th>نوع</th>
                        <th>دستگاه</th>
                        <th>انقضا</th>
                        <th>آخرین فعالیت</th>
                        <th>نسخه اپ</th>
                        <th>ثبت</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="user-email-link">
                                    <strong>{{ $user->email }}</strong>
                                </a>
                            </td>

                            <td>
                                @if($user->is_active)
                                    <span class="status-badge status-active">فعال</span>
                                @else
                                    <span class="status-badge status-inactive">غیرفعال</span>
                                @endif
                            </td>

                            <td>
                                @if($user->is_test)
                                    <span class="type-badge">تستی</span>
                                @else
                                    <span class="type-badge">عادی</span>
                                @endif
                            </td>

                            <td>
                                {{ $user->device_model ?: '—' }}
                            </td>

                            <td>
                                {{ jalali_date($user->expired_at, 'Y/m/d') }}
                            </td>

                            <td>
                                {{ jalali_date($user->last_seen) }}
                            </td>

                            <td>
                                {{ $user->app_version_code ?: '—' }}
                            </td>

                            <td>
                                {{ jalali_date($user->created_at, 'Y/m/d') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="users-pagination">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <strong>کاربری وجود ندارد</strong>
                <span>هنوز هیچ کاربری در سیستم ثبت نشده است.</span>
            </div>
        @endif

    </div>

@endsection
