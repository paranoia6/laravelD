@extends('layouts.admin')

@section('content')

    @php
        $user = auth()->user();
    @endphp

    <div class="admin-page admins-page">

        <div class="page-header">
            <div>
                <h1>مدیریت Adminها</h1>
                <p>ایجاد، شارژ، مدیریت وضعیت و مشاهده عملکرد Adminها</p>
            </div>

            <div class="page-header-badge">
                Super Admin
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('admin.admins') }}" class="admin-search-box">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="جستجوی سریع با ایمیل Admin..."
                autocomplete="off"
            >

            <button type="submit">
                جستجو
            </button>

            @if(request()->filled('search'))
                <a
                    href="{{ route('admin.admins') }}"
                    class="btn"
                    style="display:flex;align-items:center;justify-content:center;padding:0 14px;"
                >
                    پاک کردن
                </a>
            @endif
        </form>


        {{-- Create Admin --}}
        <div class="admin-card create-admin-card">

            <div class="section-title">
                <div>
                    <h2>ایجاد Admin جدید</h2>
                    <p>یک پنل جدید برای مدیریت اکانت‌ها ایجاد کنید.</p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('admin.admins.store') }}"
                class="admin-form"
            >
                @csrf

                <div class="form-grid">

                    <div class="form-group">
                        <label>ایمیل</label>

                        <input
                            type="email"
                            name="email"
                            class="admin-input"
                            value="{{ old('email') }}"
                            placeholder="admin@example.com"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>رمز عبور</label>

                        <input
                            type="password"
                            name="password"
                            class="admin-input"
                            placeholder="حداقل ۸ کاراکتر"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>تکرار رمز عبور</label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="admin-input"
                            placeholder="تکرار رمز عبور"
                            required
                        >
                    </div>

                </div>

                <button class="admin-btn primary" type="submit">
                    ایجاد Admin
                </button>
            </form>
        </div>


        {{-- Admin Cards --}}
        <div class="admins-grid">

            @forelse($admins as $admin)

                <div class="admin-card admin-user-card">

                    <div class="admin-card-top">

                        <div class="admin-user-info">

                            <div class="admin-avatar">
                                {{ strtoupper(substr($admin->email, 0, 1)) }}
                            </div>

                            <div>
                                <h3>{{ $admin->email }}</h3>

                                @if($admin->is_active)
                                    <span class="status-badge active">
                                    فعال
                                </span>
                                @else
                                    <span class="status-badge blocked">
                                    مسدود
                                </span>
                                @endif
                            </div>

                        </div>

                        <div class="balance-box">
                            <span>موجودی</span>
                            <strong>
                                {{ number_format($admin->balance ?? 0) }}
                            </strong>
                            <small>تومان</small>
                        </div>

                    </div>


                    {{-- Stats --}}
                    <div class="admin-stats">

                        <div>
                            <span>کل اکانت</span>
                            <strong>{{ $admin->total_accounts }}</strong>
                        </div>

                        <div>
                            <span>فعال</span>
                            <strong>{{ $admin->active_accounts }}</strong>
                        </div>

                        <div>
                            <span>مسدود</span>
                            <strong>{{ $admin->blocked_accounts }}</strong>
                        </div>

                        <div>
                            <span>در حال انقضا</span>
                            <strong>{{ $admin->expiring_accounts }}</strong>
                        </div>

                    </div>


                    {{-- Credit --}}
                    <div class="admin-section">

                        <div class="section-label">
                            شارژ موجودی
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.admins.credit', $admin) }}"
                            class="inline-form"
                        >
                            @csrf

                            <input
                                type="number"
                                name="amount"
                                class="admin-input"
                                min="1"
                                placeholder="مبلغ شارژ"
                                required
                            >

                            <button
                                type="submit"
                                class="admin-btn primary"
                            >
                                شارژ
                            </button>
                        </form>

                    </div>


                    {{-- Adjust --}}
                    <div class="admin-section">

                        <div class="section-label">
                            اصلاح موجودی
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.admins.credit.adjust', $admin) }}"
                            class="inline-form"
                        >
                            @csrf
                            @method('PATCH')

                            <input
                                type="number"
                                name="amount"
                                class="admin-input"
                                placeholder="+ یا - مبلغ"
                                required
                            >

                            <button
                                type="submit"
                                class="admin-btn secondary"
                            >
                                اصلاح
                            </button>
                        </form>

                    </div>


                    {{-- Block --}}
                    @if($admin->is_active)

                        <div class="admin-section block-section">

                            <div class="section-label danger-label">
                                مسدودسازی Admin
                            </div>

                            <form
                                method="POST"
                                action="{{ route('admin.admins.block', $admin) }}"
                                class="block-form"
                                onsubmit="return confirm('آیا از مسدودسازی این Admin مطمئن هستید؟');"
                            >
                                @csrf
                                @method('PATCH')

                                <label class="radio-option">
                                    <input
                                        type="radio"
                                        name="disable_accounts"
                                        value="0"
                                        checked
                                    >

                                    <span>
                                    فقط خود Admin مسدود شود
                                </span>
                                </label>

                                <label class="radio-option">
                                    <input
                                        type="radio"
                                        name="disable_accounts"
                                        value="1"
                                    >

                                    <span>
                                    Admin + تمام اکانت‌های فعال او مسدود شوند
                                </span>
                                </label>

                                <button
                                    type="submit"
                                    class="admin-btn danger"
                                >
                                    مسدود کردن Admin
                                </button>

                            </form>

                        </div>

                    @else

                        <div class="admin-section unblock-section">

                            <div class="section-label">
                                این Admin مسدود است.
                            </div>

                            @if($admin->admin_block_reason === 'admin_and_accounts')
                                <p class="muted-text">
                                    هنگام مسدودی، اکانت‌های فعال این Admin نیز غیرفعال شده‌اند.
                                </p>
                            @else
                                <p class="muted-text">
                                    فقط خود Admin مسدود شده و اکانت‌ها تغییری نکرده‌اند.
                                </p>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('admin.admins.unblock', $admin) }}"
                                onsubmit="return confirm('رفع مسدودی این Admin انجام شود؟');"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="admin-btn success"
                                >
                                    رفع مسدودی
                                </button>
                            </form>

                        </div>

                    @endif

                </div>

            @empty

                <div class="admin-card empty-state">
                    هنوز هیچ Adminای ایجاد نشده است.
                </div>

            @endforelse

        </div>


        <div class="pagination-wrapper">
            {{ $admins->links() }}
        </div>

    </div>


    <style>

        .admins-page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .page-header h1 {
            margin: 0 0 7px;
        }

        .page-header p {
            margin: 0;
            color: #777;
        }

        .page-header-badge {
            background: #fff0f6;
            color: #a14970;
            border: 1px solid #efdde7;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-card {
            background: #fff;
            border: 1px solid #efdde7;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 6px 20px rgba(130, 60, 95, .07);
        }

        .create-admin-card {
            margin-bottom: 24px;
        }

        .section-title h2 {
            margin: 0 0 5px;
            font-size: 20px;
        }

        .section-title p {
            margin: 0 0 20px;
            color: #777;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 16px;
        }

        .form-group label,
        .section-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 13px;
        }

        .admin-input {
            width: 100%;
            min-height: 44px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 8px 12px;
            font: inherit;
            background: #fff;
        }

        .admin-input:focus {
            outline: none;
            border-color: #d95d91;
            box-shadow: 0 0 0 3px rgba(217, 93, 145, .10);
        }

        .admin-btn {
            min-height: 42px;
            border: 0;
            border-radius: 10px;
            padding: 8px 16px;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            transition: .2s;
        }

        .admin-btn:hover {
            transform: translateY(-1px);
        }

        .admin-btn.primary {
            background: #d95d91;
            color: #fff;
        }

        .admin-btn.secondary {
            background: #fff0f6;
            color: #a14970;
        }

        .admin-btn.danger {
            background: #fff0f2;
            color: #c43d5d;
        }

        .admin-btn.success {
            background: #eefaf2;
            color: #218649;
        }

        .admins-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .admin-user-card {
            min-width: 0;
        }

        .admin-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .admin-avatar {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #fff0f6;
            color: #a14970;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .admin-user-info h3 {
            margin: 0 0 7px;
            font-size: 16px;
            word-break: break-word;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
        }

        .status-badge.active {
            background: #eefaf2;
            color: #218649;
        }

        .status-badge.blocked {
            background: #fff0f2;
            color: #c43d5d;
        }

        .balance-box {
            text-align: left;
            background: #faf7f9;
            border-radius: 13px;
            padding: 10px 13px;
            min-width: 120px;
        }

        .balance-box span,
        .balance-box small {
            display: block;
            color: #777;
            font-size: 11px;
        }

        .balance-box strong {
            display: block;
            margin: 4px 0;
            font-size: 19px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin: 20px 0;
        }

        .admin-stats > div {
            background: #faf7f9;
            border-radius: 12px;
            padding: 11px;
            text-align: center;
        }

        .admin-stats span {
            display: block;
            color: #777;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .admin-stats strong {
            font-size: 18px;
        }

        .admin-section {
            border-top: 1px solid #f1e6ec;
            padding-top: 16px;
            margin-top: 16px;
        }

        .inline-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
        }

        .block-section {
            background: #fffafb;
            border-radius: 13px;
            padding: 15px;
        }

        .danger-label {
            color: #c43d5d;
        }

        .block-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px;
            border: 1px solid #f0e0e7;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
        }

        .radio-option input {
            margin-top: 3px;
        }

        .muted-text {
            color: #777;
            font-size: 13px;
            line-height: 1.8;
        }

        .unblock-section {
            background: #fafdfb;
            border-radius: 13px;
            padding: 15px;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            color: #777;
            padding: 50px 20px;
        }

        .pagination-wrapper {
            margin-top: 22px;
        }

        .alert-success,
        .alert-danger {
            border-radius: 13px;
            padding: 13px 16px;
            margin-bottom: 18px;
        }

        .alert-success {
            background: #eefaf2;
            color: #218649;
        }

        .alert-danger {
            background: #fff0f2;
            color: #c43d5d;
        }

        @media (max-width: 900px) {

            .admins-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .admin-card-top {
                flex-direction: column;
            }

            .balance-box {
                width: 100%;
                box-sizing: border-box;
                text-align: right;
            }

            .admin-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .inline-form {
                grid-template-columns: 1fr;
            }

            .admin-btn {
                width: 100%;
            }
        }

    </style>

@endsection
