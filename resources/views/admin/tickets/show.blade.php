@extends('layouts.admin')

@section('title', 'مشاهده تیکت')

@section('content')

    <div class="ticket-show-page">

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

        <div class="ticket-show-header">

            <div>
                <div class="ticket-page-kicker">تیکت پشتیبانی #{{ $ticket->id }}</div>

                <h1>{{ $ticket->subject }}</h1>

                <div class="ticket-show-subtitle">
                    {{ $ticket->admin->email ?? '-' }}
                </div>
            </div>

            <a href="{{ route('admin.tickets') }}" class="ticket-back-btn">
                ← بازگشت به تیکت‌ها
            </a>

        </div>

        <div class="ticket-info-card">

            <div class="ticket-info-item">
                <span>وضعیت</span>

                <strong class="ticket-status status-{{ $ticket->status }}">
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
                </strong>
            </div>

            <div class="ticket-info-item">
                <span>اولویت</span>

                <strong class="ticket-priority priority-{{ $ticket->priority }}">
                    @switch($ticket->priority)
                        @case('high')
                            بالا
                            @break
                        @case('normal')
                            عادی
                            @break
                        @case('low')
                            پایین
                            @break
                    @endswitch
                </strong>
            </div>

            <div class="ticket-info-item">
                <span>تعداد پیام</span>
                <strong>{{ $ticket->messages->count() }}</strong>
            </div>

            <div class="ticket-info-item">
                <span>آخرین بروزرسانی</span>
                <strong>{{ jalali_date($ticket->updated_at) }}</strong>
            </div>

        </div>

        <div class="ticket-section-title">
            <span>گفت‌وگو</span>
            <small>{{ $ticket->messages->count() }} پیام</small>
        </div>

        <div class="ticket-messages">

            @foreach($ticket->messages as $message)

                @php
                    $isMe = (int) $message->user_id === (int) auth()->id();
                    $isSuperAdmin = $message->user?->role?->value === 'super_admin';
                @endphp

                <article class="ticket-message {{ $isMe ? 'mine' : 'theirs' }}">

                    <div class="ticket-message-head">

                        <div class="ticket-message-user">
                        <span class="ticket-message-avatar">
                            {{ mb_substr($message->user->email ?? 'U', 0, 1) }}
                        </span>

                            <div>
                                <strong>
                                    {{ $message->user->email ?? 'کاربر' }}
                                </strong>

                                <small>
                                    {{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}
                                </small>
                            </div>
                        </div>

                        <span class="ticket-message-date">
                        {{ jalali_date($message->created_at) }}
                    </span>

                    </div>

                    <div class="ticket-message-body">
                        {{ $message->message }}
                    </div>

                </article>

            @endforeach

        </div>

        @if($ticket->status !== 'closed')

            <div class="ticket-reply-card">

                <div class="ticket-reply-header">
                    <div>
                        <strong>ارسال پیام</strong>
                        <span>
                        @if(auth()->user()->role->value === 'admin')
                                پاسخ شما تیکت را در وضعیت «در انتظار پاسخ» نگه می‌دارد.
                            @else
                                پاسخ شما برای Admin ارسال می‌شود.
                            @endif
                    </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                    @csrf

                    <textarea
                        name="message"
                        class="ticket-reply-input"
                        rows="6"
                        maxlength="5000"
                        placeholder="متن پیام خود را بنویسید..."
                        required
                    ></textarea>

                    <div class="ticket-reply-footer">
                        <span>حداکثر ۵۰۰۰ کاراکتر</span>

                        <button type="submit" class="ticket-primary-btn">
                            ارسال پیام
                        </button>
                    </div>
                </form>

            </div>

        @else

            <div class="ticket-closed-box">
                این تیکت بسته شده است و امکان ارسال پیام جدید وجود ندارد.
            </div>

        @endif

        @if(auth()->user()->role->value === 'super_admin')

            <div class="ticket-status-card">

                <div>
                    <strong>مدیریت وضعیت تیکت</strong>
                    <span>وضعیت را برای Admin تغییر دهید.</span>
                </div>

                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                    @csrf
                    @method('PATCH')

                    <select name="status" class="ticket-status-select">

                        <option value="open" @selected($ticket->status === 'open')}>
                            باز
                        </option>

                        <option value="pending" @selected($ticket->status === 'pending')}>
                            در انتظار پاسخ
                        </option>

                        <option value="closed" @selected($ticket->status === 'closed')}>
                            بسته
                        </option>

                    </select>

                    <button type="submit" class="ticket-secondary-btn">
                        ذخیره وضعیت
                    </button>
                </form>

            </div>

        @endif

    </div>

    <style>
        .ticket-show-page {
            width: 100%;
            max-width: none;
            direction: rtl;
        }

        .ticket-show-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        .ticket-show-header h1 {
            margin: 0;
            color: #30242a;
            font-size: 22px;
            font-weight: 900;
        }

        .ticket-page-kicker {
            margin-bottom: 6px;
            color: #c64d7c;
            font-size: 11px;
            font-weight: 800;
        }

        .ticket-show-subtitle {
            margin-top: 7px;
            color: #918088;
            font-size: 11px;
            direction: ltr;
            text-align: right;
        }

        .ticket-back-btn,
        .ticket-secondary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 15px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid #ead4df;
            background: #fff;
            color: #9e456a;
            font-family: inherit;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .ticket-back-btn:hover,
        .ticket-secondary-btn:hover {
            background: #fff4f8;
            color: #b53e6c;
        }

        .ticket-info-card {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(100,50,75,.04);
            margin-bottom: 22px;
        }

        .ticket-info-item {
            min-height: 78px;
            padding: 15px 18px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            border-left: 1px solid #f1e5ea;
        }

        .ticket-info-item:last-child {
            border-left: 0;
        }

        .ticket-info-item span {
            color: #a18e97;
            font-size: 10px;
        }

        .ticket-info-item > strong:not(.ticket-status):not(.ticket-priority) {
            color: #43343b;
            font-size: 13px;
        }

        .ticket-status,
        .ticket-priority {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            min-height: 27px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
        }

        .status-open {
            color: #9f355e;
            background: #fff0f6;
        }

        .status-pending {
            color: #936000;
            background: #fff7df;
        }

        .status-closed {
            color: #6f6870;
            background: #f1eff1;
        }

        .priority-high {
            color: #b63e59;
            background: #fff0f2;
        }

        .priority-normal {
            color: #5265d9;
            background: #eef1ff;
        }

        .priority-low {
            color: #6f6870;
            background: #f1eff1;
        }

        .ticket-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 12px;
            color: #44343c;
            font-size: 14px;
            font-weight: 900;
        }

        .ticket-section-title small {
            color: #a08d96;
            font-size: 10px;
            font-weight: 600;
        }

        .ticket-messages {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .ticket-message {
            width: 100%;
            box-sizing: border-box;
            padding: 16px 18px;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(100,50,75,.035);
        }

        .ticket-message.mine {
            border-right: 4px solid #d95d91;
        }

        .ticket-message.theirs {
            border-right: 4px solid #c8b5bf;
        }

        .ticket-message-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding-bottom: 11px;
            margin-bottom: 12px;
            border-bottom: 1px solid #f3e8ed;
        }

        .ticket-message-user {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }

        .ticket-message-avatar {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #fff0f6;
            color: #b34470;
            font-size: 12px;
            font-weight: 900;
        }

        .ticket-message-user strong {
            display: block;
            max-width: 420px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            direction: ltr;
            text-align: right;
            color: #4a3941;
            font-size: 11px;
        }

        .ticket-message-user small {
            display: block;
            margin-top: 3px;
            color: #a18e97;
            font-size: 9px;
        }

        .ticket-message-date {
            color: #a18e97;
            font-size: 10px;
            white-space: nowrap;
            direction: ltr;
        }

        .ticket-message-body {
            color: #4e4148;
            font-size: 13px;
            line-height: 2;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .ticket-reply-card,
        .ticket-status-card {
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(100,50,75,.04);
            margin-top: 16px;
        }

        .ticket-reply-header {
            padding: 16px 18px;
            border-bottom: 1px solid #f2e6eb;
        }

        .ticket-reply-header strong,
        .ticket-status-card strong {
            display: block;
            color: #44343c;
            font-size: 13px;
        }

        .ticket-reply-header span,
        .ticket-status-card span {
            display: block;
            margin-top: 5px;
            color: #9a8790;
            font-size: 10px;
        }

        .ticket-reply-card form {
            padding: 16px 18px;
        }

        .ticket-reply-input {
            width: 100%;
            box-sizing: border-box;
            min-height: 135px;
            resize: vertical;
            padding: 13px;
            border: 1px solid #ead5dd;
            border-radius: 11px;
            outline: none;
            font-family: inherit;
            font-size: 12px;
            line-height: 1.9;
        }

        .ticket-reply-input:focus {
            border-color: #d987a5;
            box-shadow: 0 0 0 3px rgba(217,135,165,.12);
        }

        .ticket-reply-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-top: 12px;
        }

        .ticket-reply-footer span {
            color: #a18e97;
            font-size: 9px;
        }

        .ticket-status-card {
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .ticket-status-card form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ticket-status-select {
            min-width: 170px;
            height: 40px;
            padding: 0 11px;
            border: 1px solid #ead5dd;
            border-radius: 10px;
            background: #fff;
            font-family: inherit;
            font-size: 11px;
        }

        .ticket-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border: 0;
            border-radius: 10px;
            background: #c64d7c;
            color: #fff;
            font-family: inherit;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .ticket-primary-btn:hover {
            background: #b53e6c;
            color: #fff;
        }

        .ticket-closed-box {
            padding: 18px;
            text-align: center;
            background: #f7f5f6;
            border: 1px solid #e9e1e4;
            border-radius: 14px;
            color: #776a71;
            font-size: 12px;
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

        @media (max-width: 800px) {
            .ticket-show-header {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-info-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ticket-info-item:nth-child(2) {
                border-left: 0;
            }

            .ticket-info-item:nth-child(-n+2) {
                border-bottom: 1px solid #f1e5ea;
            }

            .ticket-status-card {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-status-card form {
                width: 100%;
            }

            .ticket-status-select,
            .ticket-secondary-btn {
                flex: 1;
            }
        }

        @media (max-width: 560px) {
            .ticket-info-card {
                grid-template-columns: 1fr;
            }

            .ticket-info-item {
                border-left: 0;
                border-bottom: 1px solid #f1e5ea;
            }

            .ticket-info-item:last-child {
                border-bottom: 0;
            }

            .ticket-message-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .ticket-reply-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-primary-btn {
                width: 100%;
            }

            .ticket-status-card form {
                flex-direction: column;
            }

            .ticket-status-select,
            .ticket-secondary-btn {
                width: 100%;
            }
        }
    </style>

@endsection
