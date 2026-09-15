@extends('layouts.admin')

@section('title', 'اعلان‌ها')

@section('content')

    <div class="notifications-page">

        <div class="notifications-header">
            <div>
                <div class="notifications-kicker">مرکز اطلاع‌رسانی</div>
                <h1>اعلان‌ها</h1>
                <p>تمام اتفاقات مهم مربوط به حساب شما در این قسمت نمایش داده می‌شود.</p>
            </div>

            <div class="notifications-count">
                {{ $notifications->total() }} اعلان
            </div>
        </div>

        <div class="notifications-list">

            @forelse($notifications as $notification)

                @php
                    $data = $notification->data ?? [];
                    $level = $data['level'] ?? 'info';
                @endphp

                <article class="notification-card {{ $notification->read_at ? 'read' : 'unread' }} level-{{ $level }}">

                    <div class="notification-icon">
                        @if($level === 'success')
                            ✓
                        @elseif($level === 'warning')
                            !
                        @elseif($level === 'danger')
                            ×
                        @else
                            i
                        @endif
                    </div>

                    <div class="notification-content">

                        <div class="notification-top">
                            <strong>
                                {{ $data['title'] ?? 'اعلان سیستم' }}
                            </strong>

                            @if(!$notification->read_at)
                                <span class="notification-new">جدید</span>
                            @endif
                        </div>

                        <div class="notification-message">
                            {{ $data['message'] ?? '-' }}
                        </div>

                        <div class="notification-date">
                            {{ jalali_date($notification->created_at, 'Y/m/d H:i') }}
                        </div>

                    </div>

                    <div class="notification-actions">

                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}" class="notification-view">
                                مشاهده
                            </a>
                        @endif

                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="notification-read">
                                    خوانده شد
                                </button>
                            </form>
                        @endif

                    </div>

                </article>

            @empty

                <div class="notification-empty">
                    <div class="notification-empty-icon">✓</div>
                    <strong>اعلان جدیدی وجود ندارد.</strong>
                    <span>هر اعلان مهم سیستم در اینجا نمایش داده می‌شود.</span>
                </div>

            @endforelse

        </div>

        <div class="notification-pagination">
            {{ $notifications->links() }}
        </div>

    </div>

    <style>
        .notifications-page {
            width: 100%;
            direction: rtl;
        }

        .notifications-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .notifications-kicker {
            color: #c64d7c;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .notifications-header h1 {
            margin: 0;
            color: #30242a;
            font-size: 24px;
            font-weight: 900;
        }

        .notifications-header p {
            margin: 6px 0 0;
            color: #918088;
            font-size: 11px;
        }

        .notifications-count {
            padding: 9px 13px;
            border-radius: 10px;
            background: #fff0f6;
            color: #b34470;
            border: 1px solid #efd4df;
            font-size: 10px;
            font-weight: 800;
        }

        .notifications-list {
            display: flex;
            flex-direction: column;
            gap: 11px;
        }

        .notification-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 17px 18px;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(100,50,75,.04);
        }

        .notification-card.unread {
            background: #fffafd;
        }

        .notification-card.unread::before {
            content: "";
            position: absolute;
            right: 0;
            top: 13px;
            bottom: 13px;
            width: 4px;
            border-radius: 5px 0 0 5px;
            background: #d95d91;
        }

        .notification-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fff0f6;
            color: #c64d7c;
            font-size: 17px;
            font-weight: 900;
        }

        .level-success .notification-icon {
            background: #edf9f3;
            color: #2c8a5e;
        }

        .level-warning .notification-icon {
            background: #fff7df;
            color: #a26a00;
        }

        .level-danger .notification-icon {
            background: #fff0f2;
            color: #c43d5d;
        }

        .notification-content {
            min-width: 0;
            flex: 1;
        }

        .notification-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 7px;
        }

        .notification-top strong {
            color: #3e3037;
            font-size: 13px;
        }

        .notification-new {
            padding: 3px 7px;
            border-radius: 999px;
            background: #d95d91;
            color: #fff;
            font-size: 8px;
            font-weight: 800;
        }

        .notification-message {
            color: #65565e;
            font-size: 11px;
            line-height: 1.9;
        }

        .notification-date {
            margin-top: 7px;
            color: #a08d96;
            font-size: 9px;
            direction: ltr;
            text-align: right;
        }

        .notification-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .notification-view,
        .notification-read {
            min-height: 34px;
            padding: 0 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            border: 1px solid #ead4df;
            background: #fff;
            color: #a4486e;
            text-decoration: none;
            font-family: inherit;
            font-size: 9px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }

        .notification-read {
            background: #fff0f6;
            border-color: #f0cddc;
        }

        .notification-view:hover,
        .notification-read:hover {
            background: #c64d7c;
            color: #fff;
            border-color: #c64d7c;
        }

        .notification-card.read {
            opacity: .82;
        }

        .notification-empty {
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 17px;
        }

        .notification-empty-icon {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #edf9f3;
            color: #2c8a5e;
            font-size: 21px;
            font-weight: 900;
        }

        .notification-empty strong {
            color: #5b4b53;
            font-size: 13px;
        }

        .notification-empty span {
            color: #a08d96;
            font-size: 10px;
        }

        .notification-pagination {
            margin-top: 18px;
        }

        @media (max-width: 650px) {
            .notifications-header,
            .notification-card {
                align-items: stretch;
                flex-direction: column;
            }

            .notifications-count {
                width: fit-content;
            }

            .notification-actions {
                width: 100%;
            }

            .notification-view,
            .notification-read {
                flex: 1;
            }
        }
    </style>

@endsection
