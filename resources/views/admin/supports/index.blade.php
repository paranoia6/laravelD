@extends('layouts.admin')

@section('title', 'پشتیبانی')

@section('content')

    <div class="admin-page">

        <div class="page-header">

            <div>
                <h1>پشتیبانی</h1>

                <p>
                    برای هر پشتیبانی یک نام تعیین کنید و شبکه‌های موردنیاز را ثبت کنید.
                </p>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                id="open-support-form"
            >
                + افزودن پشتیبانی
            </button>

        </div>


        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-danger">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        {{-- ایجاد پشتیبانی جدید --}}
        <div
            class="support-create-card"
            id="support-create-card"
            style="display: none;"
        >

            <div class="support-create-header">

                <div>
                    <h2>افزودن پشتیبانی جدید</h2>

                    <p>
                        نام پشتیبانی و اطلاعات شبکه‌های موردنظر را وارد کنید.
                    </p>
                </div>

                <button
                    type="button"
                    class="support-close-btn"
                    id="close-support-form"
                >
                    ×
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('admin.supports.store') }}"
                class="support-create-form"
            >

                @csrf


                <div class="form-section">

                    <label for="support-name">
                        نام پشتیبانی
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="support-name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="مثلاً پشتیبانی VIP"
                        maxlength="100"
                        required
                    >

                    <small class="help">
                        این نام در زمان ساخت اکانت به کاربر نمایش داده می‌شود.
                    </small>

                </div>


                <div class="network-section">

                    <div class="section-title">
                        شبکه‌های پشتیبانی
                    </div>


                    <div class="network-grid">

                        @foreach(\App\Models\Support::networks() as $network => $label)

                            @php
                                $logo = match($network) {
                                    'whatsapp' => 'WA',
                                    'instagram' => 'IG',
                                    'telegram' => 'TG',
                                    'rubika' => 'RB',
                                    'eitaa' => 'EA',
                                    'bale' => 'BL',
                                    default => mb_substr($label, 0, 2),
                                };

                                $placeholder = $network === 'whatsapp'
                                    ? '989121234567'
                                    : 'your_id';
                            @endphp


                            <div class="network-input-card">

                                <div class="network-input-header">

                                    <div class="support-network-logo support-logo-{{ $network }}">
                                        {{ $logo }}
                                    </div>

                                    <div>

                                        <strong>
                                            {{ $label }}
                                        </strong>

                                        <small>
                                            {{ $network === 'whatsapp' ? 'شماره یا لینک' : 'آیدی یا لینک' }}
                                        </small>

                                    </div>

                                </div>


                                <input
                                    type="text"
                                    name="links[{{ $network }}]"
                                    class="form-control"
                                    value="{{ old('links.' . $network) }}"
                                    placeholder="{{ $placeholder }}"
                                    maxlength="255"
                                    autocomplete="off"
                                    @if($network === 'whatsapp')
                                        inputmode="numeric"
                                    @else
                                        inputmode="text"
                                    @endif
                                >


                                @if($network === 'whatsapp')

                                    <small class="help">
                                        با کد کشور و بدون + یا فاصله وارد کنید.
                                        مثال: 989121234567
                                    </small>

                                @else

                                    <small class="help">
                                        فقط آیدی را وارد کنید؛ لینک توسط سیستم ساخته می‌شود.
                                    </small>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </div>


                <div class="support-create-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        ثبت پشتیبانی
                    </button>

                    <button
                        type="button"
                        class="btn btn-light"
                        id="cancel-support-form"
                    >
                        انصراف
                    </button>

                </div>

            </form>

        </div>


        @if($supports->count())

            <div class="support-grid">

                @foreach($supports as $support)

                    <div class="support-card">

                        <div class="support-card-header">

                            <div class="support-title-wrap">

                                <div class="support-network-logo support-logo-telegram">
                                    {{ mb_substr($support->name, 0, 2) }}
                                </div>


                                <div>

                                    <h3>
                                        {{ $support->name }}
                                    </h3>


                                    @if($support->is_active)

                                        <span class="support-status active">
                                            فعال
                                        </span>

                                    @else

                                        <span class="support-status empty">
                                            غیرفعال
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="support-links-list">

                            @foreach(\App\Models\Support::networks() as $network => $label)

                                @php
                                    $link = $support->getNetworkLink($network);

                                    $logo = match($network) {
                                        'whatsapp' => 'WA',
                                        'instagram' => 'IG',
                                        'telegram' => 'TG',
                                        'rubika' => 'RB',
                                        'eitaa' => 'EA',
                                        'bale' => 'BL',
                                        default => mb_substr($label, 0, 2),
                                    };
                                @endphp


                                <div class="support-link-row">

                                    <div class="support-link-title">

                                        <div class="support-network-logo support-logo-{{ $network }}">
                                            {{ $logo }}
                                        </div>

                                        <div>

                                            <strong>
                                                {{ $label }}
                                            </strong>

                                            @if($link)

                                                <span class="support-status active">
                                                    ثبت شده
                                                </span>

                                            @else

                                                <span class="support-status empty">
                                                    ثبت نشده
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    @if($link)

                                        <a
                                            href="{{ $link }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="support-link-value"
                                        >
                                            {{ $link }}
                                        </a>

                                    @else

                                        <span class="support-link-empty">
                                            —
                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>


                        <div class="support-actions">

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.supports.toggle',
                                    $support
                                ) }}"
                            >

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-light"
                                >
                                    {{ $support->is_active
                                        ? 'غیرفعال کردن'
                                        : 'فعال کردن'
                                    }}
                                </button>

                            </form>


                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.supports.destroy',
                                    $support
                                ) }}"
                                onsubmit="return confirm('این پشتیبانی حذف شود؟')"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    حذف
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="support-empty">

                <h3>
                    هنوز پشتیبانی‌ای ثبت نشده است.
                </h3>

                <p>
                    برای شروع، یک پشتیبانی جدید ایجاد کنید.
                </p>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="open-empty-support-form"
                >
                    + افزودن پشتیبانی
                </button>

            </div>

        @endif

    </div>


    <style>

        .support-grid {
            display: grid;
            grid-template-columns:
            repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .support-card {
            background: #fff;
            border: 1px solid #eadde4;
            border-radius: 18px;
            padding: 22px;
            box-shadow:
                0 5px 18px
                rgba(120, 60, 90, .06);
        }

        .support-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            min-height: 48px;
        }

        .support-title-wrap {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .support-card-header h3 {
            margin: 0 0 7px;
            font-size: 15px;
            font-weight: 900;
        }

        .support-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        .support-status.active {
            background: #e8f7ee;
            color: #168344;
        }

        .support-status.empty {
            background: #fff2e8;
            color: #c55a11;
        }

        .support-network-logo {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .3px;
        }

        .support-logo-whatsapp {
            background: #e8f8ef;
            color: #20a95b;
        }

        .support-logo-instagram {
            background: #faeafa;
            color: #c135b7;
        }

        .support-logo-telegram {
            background: #e8f5fd;
            color: #229ed9;
        }

        .support-logo-rubika {
            background: #fff0e5;
            color: #e87528;
        }

        .support-logo-eitaa {
            background: #edf0ff;
            color: #405de6;
        }

        .support-logo-bale {
            background: #e8f7ff;
            color: #168dcc;
        }

        .support-links-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }

        .support-link-row {
            padding: 11px;
            background: #f8f5f7;
            border-radius: 12px;
        }

        .support-link-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 8px;
        }

        .support-link-title .support-network-logo {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
            border-radius: 10px;
            font-size: 10px;
        }

        .support-link-title strong {
            display: inline-block;
            margin-left: 5px;
        }

        .support-link-value {
            display: block;
            word-break: break-all;
            color: #6b3f5b;
            font-size: 13px;
        }

        .support-link-empty {
            color: #999;
            font-size: 13px;
        }

        .support-actions {
            display: flex;
            gap: 8px;
        }

        .support-actions form {
            flex: 1;
        }

        .support-actions button {
            width: 100%;
        }

        .support-create-card {
            background: #fff;
            border: 1px solid #eadde4;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow:
                0 5px 18px
                rgba(120, 60, 90, .06);
        }

        .support-create-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .support-create-header h2 {
            margin: 0 0 7px;
        }

        .support-create-header p {
            margin: 0;
            opacity: .7;
        }

        .support-close-btn {
            border: 0;
            background: transparent;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            opacity: .6;
        }

        .form-section {
            margin-bottom: 22px;
        }

        .form-section label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .help {
            display: block;
            margin-top: 6px;
            opacity: .7;
            font-size: 12px;
            line-height: 1.7;
        }

        .network-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: 800;
            margin-bottom: 12px;
        }

        .network-grid {
            display: grid;
            grid-template-columns:
            repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .network-input-card {
            border: 1px solid #eadde4;
            border-radius: 14px;
            padding: 15px;
            background: #fcfafb;
        }

        .network-input-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 11px;
        }

        .network-input-header strong {
            display: block;
            margin-bottom: 3px;
        }

        .network-input-header small {
            display: block;
            opacity: .65;
            font-size: 11px;
        }

        .support-create-actions {
            display: flex;
            gap: 8px;
        }

        .support-empty {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
            border: 1px solid #eadde4;
            border-radius: 18px;
        }

        .support-empty h3 {
            margin: 0 0 8px;
        }

        .support-empty p {
            margin: 0 0 18px;
            opacity: .7;
        }

        @media(max-width: 1000px) {

            .support-grid {
                grid-template-columns:
                repeat(2, minmax(0, 1fr));
            }

            .network-grid {
                grid-template-columns:
                repeat(2, minmax(0, 1fr));
            }

        }

        @media(max-width: 650px) {

            .support-grid {
                grid-template-columns: 1fr;
            }

            .network-grid {
                grid-template-columns: 1fr;
            }

            .support-create-actions {
                flex-direction: column;
            }

        }

    </style>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const createCard =
                    document.getElementById(
                        'support-create-card'
                    );

                const openButton =
                    document.getElementById(
                        'open-support-form'
                    );

                const emptyButton =
                    document.getElementById(
                        'open-empty-support-form'
                    );

                const closeButton =
                    document.getElementById(
                        'close-support-form'
                    );

                const cancelButton =
                    document.getElementById(
                        'cancel-support-form'
                    );


                function openForm() {

                    if (!createCard) {
                        return;
                    }

                    createCard.style.display =
                        'block';

                    createCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    const nameInput =
                        document.getElementById(
                            'support-name'
                        );

                    if (nameInput) {

                        setTimeout(
                            function () {
                                nameInput.focus();
                            },
                            250
                        );

                    }

                }


                function closeForm() {

                    if (!createCard) {
                        return;
                    }

                    createCard.style.display =
                        'none';

                }


                if (openButton) {

                    openButton.addEventListener(
                        'click',
                        openForm
                    );

                }


                if (emptyButton) {

                    emptyButton.addEventListener(
                        'click',
                        openForm
                    );

                }


                if (closeButton) {

                    closeButton.addEventListener(
                        'click',
                        closeForm
                    );

                }


                if (cancelButton) {

                    cancelButton.addEventListener(
                        'click',
                        closeForm
                    );

                }


                @if($errors->any())

                if (createCard) {
                    createCard.style.display =
                        'block';
                }

                @endif

            }
        );

    </script>

@endsection
