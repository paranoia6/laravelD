@extends('layouts.admin')

@section('title', 'ایجاد تیکت')

@section('content')

    <div class="ticket-create-page">

        <div class="ticket-create-header">
            <div>
                <div class="ticket-page-kicker">پشتیبانی</div>
                <h1>ایجاد تیکت جدید</h1>
                <p>درخواست یا مشکل خود را برای Super Admin ارسال کنید.</p>
            </div>

            <a href="{{ route('admin.tickets') }}" class="ticket-back-btn">
                ← بازگشت
            </a>
        </div>

        @if($errors->any())
            <div class="ticket-alert ticket-alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="ticket-create-card">

            <form method="POST" action="{{ route('admin.tickets.store') }}">
                @csrf

                <div class="ticket-form-grid">

                    <div class="ticket-form-group ticket-form-full">
                        <label>موضوع تیکت</label>
                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            maxlength="255"
                            placeholder="مثلاً درخواست شارژ پنل"
                            required
                        >
                    </div>

                    <div class="ticket-form-group">
                        <label>اولویت</label>

                        <select name="priority" required>
                            <option value="normal" @selected(old('priority', 'normal') === 'normal')}>
                                عادی
                            </option>
                            <option value="high" @selected(old('priority') === 'high')}>
                                بالا
                            </option>
                            <option value="low" @selected(old('priority') === 'low')}>
                                پایین
                            </option>
                        </select>
                    </div>

                    <div class="ticket-form-note">
                        <strong>وضعیت اولیه</strong>
                        <span>در انتظار پاسخ Super Admin</span>
                    </div>

                    <div class="ticket-form-group ticket-form-full">
                        <label>متن پیام</label>

                        <textarea
                            name="message"
                            rows="9"
                            maxlength="5000"
                            placeholder="متن درخواست خود را کامل بنویسید..."
                            required
                        >{{ old('message') }}</textarea>
                    </div>

                </div>

                <div class="ticket-create-footer">
                    <span>پس از ثبت، تیکت در وضعیت «در انتظار پاسخ» قرار می‌گیرد.</span>

                    <button type="submit" class="ticket-primary-btn">
                        ثبت و ارسال تیکت
                    </button>
                </div>

            </form>

        </div>

    </div>

    <style>
        .ticket-create-page {
            width: 100%;
            direction: rtl;
        }

        .ticket-create-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .ticket-create-header h1 {
            margin: 0;
            color: #30242a;
            font-size: 23px;
            font-weight: 900;
        }

        .ticket-create-header p {
            margin: 6px 0 0;
            color: #918088;
            font-size: 11px;
        }

        .ticket-page-kicker {
            margin-bottom: 6px;
            color: #c64d7c;
            font-size: 11px;
            font-weight: 800;
        }

        .ticket-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 15px;
            border: 1px solid #ead4df;
            border-radius: 10px;
            background: #fff;
            color: #9e456a;
            text-decoration: none;
            font-family: inherit;
            font-size: 11px;
            font-weight: 800;
        }

        .ticket-create-card {
            width: 100%;
            box-sizing: border-box;
            padding: 22px;
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 17px;
            box-shadow: 0 6px 22px rgba(100,50,75,.045);
        }

        .ticket-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .ticket-form-full {
            grid-column: 1 / -1;
        }

        .ticket-form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .ticket-form-group label {
            color: #5d4b53;
            font-size: 11px;
            font-weight: 800;
        }

        .ticket-form-group input,
        .ticket-form-group select,
        .ticket-form-group textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ead5dd;
            border-radius: 10px;
            background: #fff;
            color: #493943;
            outline: none;
            font-family: inherit;
            font-size: 12px;
        }

        .ticket-form-group input,
        .ticket-form-group select {
            height: 42px;
            padding: 0 12px;
        }

        .ticket-form-group textarea {
            padding: 12px;
            line-height: 1.9;
            resize: vertical;
        }

        .ticket-form-group input:focus,
        .ticket-form-group select:focus,
        .ticket-form-group textarea:focus {
            border-color: #d987a5;
            box-shadow: 0 0 0 3px rgba(217,135,165,.12);
        }

        .ticket-form-note {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            padding: 0 14px;
            border-radius: 11px;
            background: #fff8fb;
            border: 1px solid #f0dce5;
        }

        .ticket-form-note strong {
            color: #5e4b54;
            font-size: 11px;
        }

        .ticket-form-note span {
            color: #c64d7c;
            font-size: 10px;
            font-weight: 800;
        }

        .ticket-create-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #f2e6eb;
        }

        .ticket-create-footer span {
            color: #9a8790;
            font-size: 10px;
        }

        .ticket-primary-btn {
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 11px;
            background: #c64d7c;
            color: #fff;
            font-family: inherit;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .ticket-primary-btn:hover {
            background: #b53e6c;
        }

        .ticket-alert {
            padding: 13px 16px;
            margin-bottom: 16px;
            border-radius: 11px;
            font-size: 12px;
            font-weight: 700;
        }

        .ticket-alert-danger {
            background: #fff0f3;
            border: 1px solid #efd0d9;
            color: #b23d5c;
        }

        @media (max-width: 700px) {
            .ticket-create-header,
            .ticket-create-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .ticket-form-grid {
                grid-template-columns: 1fr;
            }

            .ticket-form-full {
                grid-column: auto;
            }

            .ticket-primary-btn,
            .ticket-back-btn {
                width: 100%;
            }
        }
    </style>

@endsection
