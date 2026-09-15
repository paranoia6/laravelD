@extends('layouts.admin')

@section('content')

    <div class="admin-page audit-page">

        <div class="page-header">
            <div>
                <h1>گزارش فعالیت‌ها</h1>
                <p>
                    سوابق دائمی عملیات مهم سیستم
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        <div class="audit-wrapper">

            <table class="audit-table">

                <thead>
                <tr>
                    <th>کاربر</th>
                    <th>عملیات</th>
                    <th>توضیحات</th>
                    <th>اطلاعات</th>
                    <th>تاریخ</th>
                </tr>
                </thead>

                <tbody>

                @forelse($logs as $log)

                    <tr>

                        <td>
                            {{ $log->user?->email ?? 'سیستم' }}
                        </td>

                        <td>
                            <span class="action-badge">
                                {{ $log->action }}
                            </span>
                        </td>

                        <td>
                            {{ $log->description ?? '-' }}
                        </td>

                        <td>

                            @if(!empty($log->metadata))

                                <details>
                                    <summary>مشاهده</summary>

                                    <pre>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>

                            @else

                                -

                            @endif

                        </td>

                        <td>
                            {{ jalali_date($log->created_at, 'Y/m/d H:i:s') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="empty-state">
                            هنوز گزارشی ثبت نشده است.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        <div class="pagination-wrapper">
            {{ $logs->links() }}
        </div>

    </div>


    <style>

        .audit-page {
            max-width:1200px;
            margin:0 auto;
        }

        .page-header {
            margin-bottom:24px;
        }

        .page-header h1 {
            margin:0 0 6px;
        }

        .page-header p {
            margin:0;
            color:#777;
        }

        .audit-wrapper {
            background:#fff;
            border:1px solid #efdde7;
            border-radius:18px;
            overflow:auto;
            box-shadow:0 6px 20px rgba(130,60,95,.06);
        }

        .audit-table {
            width:100%;
            min-width:900px;
            border-collapse:collapse;
        }

        .audit-table th,
        .audit-table td {
            padding:15px 17px;
            text-align:right;
            border-bottom:1px solid #f2e8ed;
            vertical-align:top;
        }

        .audit-table th {
            background:#faf7f9;
            color:#666;
            font-size:12px;
        }

        .audit-table tbody tr:last-child td {
            border-bottom:0;
        }

        .action-badge {
            display:inline-block;
            padding:6px 10px;
            border-radius:999px;
            background:#fff0f6;
            color:#a14970;
            font-size:11px;
        }

        details summary {
            cursor:pointer;
            color:#a14970;
            font-weight:600;
        }

        details pre {
            margin-top:10px;
            padding:10px;
            border-radius:10px;
            background:#faf7f9;
            direction:ltr;
            text-align:left;
            font-size:11px;
            max-width:400px;
            overflow:auto;
        }

        .empty-state {
            text-align:center !important;
            color:#888;
            padding:35px !important;
        }

        .pagination-wrapper {
            margin-top:20px;
        }

        @media (max-width:700px) {

            .audit-wrapper {
                border-radius:14px;
            }

        }

    </style>

@endsection
