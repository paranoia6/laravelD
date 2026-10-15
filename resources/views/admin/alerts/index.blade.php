@extends('layouts.admin')

@section('title', 'هشدارها')
@section('page_title', 'هشدارها')

@section('content')

    <div class="page-header">
        <div>
            <h1>هشدارها</h1>
            <p>مدیریت پیام‌ها و هشدارهای اپلیکیشن</p>
        </div>
    </div>

    <div class="dashboard-section users-section">

        <div class="section-heading">
            <div>
                <h2>لیست هشدارها</h2>
                <p>{{ number_format($alerts->total()) }} هشدار ثبت شده است</p>
            </div>
        </div>

        @if($alerts->count())

            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                    <tr>
                        <th>عنوان</th>
                        <th>نوع</th>
                        <th>وضعیت</th>
                        <th>اولویت</th>
                        <th>شروع</th>
                        <th>پایان</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($alerts as $alert)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('admin.alerts.show', $alert) }}"
                                    class="user-email-link"
                                >
                                    <strong>{{ $alert->title }}</strong>
                                </a>
                            </td>

                            <td>
                                <span class="type-badge">
                                    {{ $alert->type?->value ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if($alert->is_active)
                                    <span class="status-badge status-active">
                                        فعال
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        غیرفعال
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $alert->priority }}
                            </td>

                            <td>
                                {{ jalali_date($alert->starts_at) }}
                            </td>

                            <td>
                                {{ jalali_date($alert->ends_at) }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('admin.alerts.show', $alert) }}"
                                    class="user-email-link"
                                >
                                    مشاهده
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="users-pagination">
                {{ $alerts->links() }}
            </div>

        @else

            <div class="support-empty-state">
                <div class="support-empty-icon">!</div>

                <strong>هشداری وجود ندارد</strong>

                <span>
                هنوز هیچ هشداری در سیستم ثبت نشده است.
            </span>
            </div>

        @endif

    </div>

@endsection
