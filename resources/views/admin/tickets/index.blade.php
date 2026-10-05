@extends('layouts.admin')

@section('title', 'تیکت‌ها')

@section('content')

    <div class="ticket-page">

        <div class="ticket-page-header">
            <div>
                <div class="ticket-page-kicker">پشتیبانی</div>
                <h1>تیکت‌ها</h1>
                <p>مدیریت درخواست‌ها و پیام‌های پشتیبانی</p>
            </div>

            @if(auth()->user()->role->value === 'admin')
                <a href="{{ route('admin.tickets.create') }}" class="ticket-primary-btn">
                    <span class="ticket-btn-icon">+</span>
                    ایجاد تیکت جدید
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="ticket-alert ticket-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="ticket-alert ticket-alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($tickets->count())

            <div class="ticket-list">

                @foreach($tickets as $ticket)

                    <article class="ticket-list-card">

                        <div class="ticket-card-main">

                            <div class="ticket-card-top">

                                <div class="ticket-id">
                                    #{{ $ticket->id }}
                                </div>

                                @if(auth()->user()->role->value === 'super_admin')
                                    <div class="ticket-admin">
                                        {{ $ticket->admin->email ?? '-' }}
                                    </div>
                                @endif

                                <div class="ticket-date">
                                    {{ jalali_date($ticket->updated_at) }}
                                </div>

                            </div>

                            <div class="ticket-card-title">
                                {{ $ticket->subject }}
                            </div>

                            <div class="ticket-card-meta">

                            <span class="ticket-status status-{{ $ticket->status }}">
                                @switch($ticket->status)
                                    @case('open')
                                        باز
                                        @break
                                    @case('pending')
                                        در انتظار پاسخ
                                        @break
                                    @case('closed')
                                        بسته
                                        @break
                                @endswitch
                            </span>

                                <span class="ticket-priority priority-{{ $ticket->priority }}">
                                @switch($ticket->priority)
                                        @case('high')
                                            اولویت بالا
                                            @break
                                        @case('normal')
                                            عادی
                                            @break
                                        @case('low')
                                            اولویت پایین
                                            @break
                                    @endswitch
                            </span>

                                <span class="ticket-message-count">
                                {{ $ticket->messages_count }} پیام
                            </span>

                            </div>

                        </div>

                        <a
                            href="{{ route('admin.tickets.show', $ticket) }}"
                            class="ticket-view-btn"
                        >
                            مشاهده تیکت
                            <span>←</span>
                        </a>

                    </article>

                @endforeach

            </div>

            <div class="ticket-pagination">
                {{ $tickets->links() }}
            </div>

        @else

            <div class="ticket-empty">
                <div class="ticket-empty-icon">ت</div>
                <strong>هنوز تیکتی ثبت نشده است.</strong>

                @if(auth()->user()->role->value === 'admin')
                    <a href="{{ route('admin.tickets.create') }}" class="ticket-primary-btn">
                        ایجاد اولین تیکت
                    </a>
                @endif
            </div>

        @endif

    </div>

    <style>
        .ticket-page {
            width: 100%;
            max-width: none;
            margin: 0;
            direction: rtl;
        }

        .ticket-page-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .ticket-page-kicker {
            color: #c64d7c;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .ticket-page-header h1 {
            margin: 0;
            color: #30242a;
            font-size: 24px;
            font-weight: 900;
        }

        .ticket-page-header p {
            margin: 6px 0 0;
            color: #8e7d85;
            font-size: 12px;
        }

        .ticket-primary-btn,
        .ticket-view-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 11px;
            text-decoration: none;
            font-family: inherit;
            font-size: 12px;
            font-weight: 800;
            transition: .2s ease;
        }

        .ticket-primary-btn {
            background: #c64d7c;
            color: #fff;
            box-shadow: 0 7px 18px rgba(198,77,124,.18);
        }

        .ticket-primary-btn:hover {
            background: #b53e6c;
            color: #fff;
            transform: translateY(-1px);
        }

        .ticket-btn-icon {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: rgba(255,255,255,.18);
            font-size: 17px;
        }

        .ticket-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ticket-list-card {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            width: 100%;
            min-height: 108px;
            padding: 18px 20px;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(100,50,75,.045);
            overflow: hidden;
        }

        .ticket-list-card::before {
            content: "";
            position: absolute;
            right: 0;
            top: 14px;
            bottom: 14px;
            width: 4px;
            border-radius: 5px 0 0 5px;
            background: #d95d91;
        }

        .ticket-card-main {
            min-width: 0;
            flex: 1;
        }

        .ticket-card-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 9px;
            color: #9b8991;
            font-size: 10px;
        }

        .ticket-id {
            color: #c64d7c;
            font-weight: 900;
        }

        .ticket-admin {
            direction: ltr;
            color: #6d5b63;
            font-weight: 700;
        }

        .ticket-date {
            margin-right: auto;
            direction: ltr;
            color: #a18f97;
        }

        .ticket-card-title {
            color: #33272d;
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ticket-card-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .ticket-status,
        .ticket-priority,
        .ticket-message-count {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
        }

        .ticket-status.status-open {
            color: #9f355e;
            background: #fff0f6;
        }

        .ticket-status.status-pending {
            color: #936000;
            background: #fff7df;
        }

        .ticket-status.status-closed {
            color: #6f6870;
            background: #f1eff1;
        }

        .ticket-priority.priority-high {
            color: #b63e59;
            background: #fff0f2;
        }

        .ticket-priority.priority-normal {
            color: #5265d9;
            background: #eef1ff;
        }

        .ticket-priority.priority-low {
            color: #6f6870;
            background: #f1eff1;
        }

        .ticket-message-count {
            color: #806d76;
            background: #faf6f8;
        }

        .ticket-view-btn {
            flex: 0 0 auto;
            background: #fff7fa;
            color: #b34470;
            border: 1px solid #efd4df;
        }

        .ticket-view-btn:hover {
            background: #c64d7c;
            color: #fff;
            border-color: #c64d7c;
        }

        .ticket-alert {
            padding: 13px 16px;
            margin-bottom: 16px;
            border-radius: 11px;
            font-size: 12px;
            font-weight: 700;
        }

        .ticket-alert-success {
            background: #edf9f3;
            border: 1px solid #d6eedf;
            color: #28754f;
        }

        .ticket-alert-danger {
            background: #fff0f3;
            border: 1px solid #efd0d9;
            color: #b23d5c;
        }

        .ticket-empty {
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 14px;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 18px;
        }

        .ticket-empty-icon {
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 17px;
            background: #fff0f6;
            color: #c64d7c;
            font-size: 22px;
            font-weight: 900;
        }

        .ticket-empty strong {
            color: #6d5b63;
            font-size: 13px;
        }

        .ticket-pagination {
            margin-top: 18px;
        }

        @media (max-width: 700px) {
            .ticket-page-header {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-primary-btn {
                width: 100%;
            }

            .ticket-list-card {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-view-btn {
                width: 100%;
            }

            .ticket-date {
                margin-right: 0;
            }
        }
    </style>

@endsection
