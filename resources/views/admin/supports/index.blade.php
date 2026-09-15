@extends('layouts.admin')

@section('title', 'پشتیبانی')

@section('content')

    <div class="admin-page">

        <div class="page-header">

            <div>
                <h1>پشتیبانی</h1>

                <p>
                    اطلاعات تماس هر پیام‌رسان را جداگانه ثبت کنید.
                </p>
            </div>

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


        <div class="support-grid">

            @foreach($supports as $support)

                @php
                    $network = $support->network_key;

                    $label = $support->network_label;

                    $link = $support->links->first();

                    $inputLabel = $network === 'whatsapp'
                        ? 'شماره واتساپ'
                        : 'آیدی';

                    $placeholder = match($network) {
                        'whatsapp' => '989121234567',
                        'instagram' => 'your_id',
                        'telegram' => 'your_id',
                        'rubika' => 'your_id',
                        'eitaa' => 'your_id',
                        'bale' => 'your_id',
                        default => 'شناسه',
                    };
                @endphp


                <div class="support-card">

                    <div class="support-card-header">

                        <div class="support-title-wrap">

                            <div class="support-network-logo support-logo-{{ $network }}">

                                @switch($network)

                                    @case('whatsapp')
                                        WA
                                        @break

                                    @case('instagram')
                                        IG
                                        @break

                                    @case('telegram')
                                        TG
                                        @break

                                    @case('rubika')
                                        RB
                                        @break

                                    @case('eitaa')
                                        EA
                                        @break

                                    @case('bale')
                                        BL
                                        @break

                                    @default
                                        {{ mb_substr($label, 0, 2) }}

                                @endswitch

                            </div>


                            <div>

                                <h3>
                                    {{ $label }}
                                </h3>

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

                    </div>


                    @if($link)

                        <div class="support-value">

                            <small>
                                لینک فعال:
                            </small>

                            <a
                                href="{{ $link->link }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                {{ $link->link }}
                            </a>

                        </div>


                        <div class="support-actions">

                            <form
                                method="POST"
                                action="{{ route(
                                'admin.support-links.toggle',
                                $link
                            ) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-light"
                                >
                                    {{ $link->is_active
                                        ? 'غیرفعال کردن'
                                        : 'فعال کردن'
                                    }}
                                </button>

                            </form>


                            <form
                                method="POST"
                                action="{{ route(
                                'admin.support-links.destroy',
                                $link
                            ) }}"
                                onsubmit="return confirm('اطلاعات این شبکه حذف شود؟')"
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


                    @else

                        <form
                            method="POST"
                            action="{{ route(
                            'admin.supports.store',
                            $support
                        ) }}"
                            class="support-form"
                        >

                            @csrf


                            <label>
                                {{ $inputLabel }}
                            </label>


                            <input
                                type="text"
                                name="value"
                                class="form-control"
                                placeholder="{{ $placeholder }}"
                                autocomplete="off"
                                required
                                maxlength="255"
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


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                ثبت {{ $label }}
                            </button>

                        </form>

                    @endif

                </div>

            @endforeach

        </div>

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
        }

        .support-card-header h3 {
            margin: 0 0 8px;
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

        .support-value {
            padding: 13px;
            background: #f8f5f7;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .support-value small {
            display: block;
            margin-bottom: 7px;
            opacity: .7;
        }

        .support-value a {
            display: block;
            word-break: break-all;
            color: #6b3f5b;
        }

        .support-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .support-form label {
            font-weight: 600;
        }

        .support-form .help {
            opacity: .7;
            font-size: 12px;
            line-height: 1.7;
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

        @media(max-width: 1000px) {
            .support-grid {
                grid-template-columns:
                repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width: 650px) {
            .support-grid {
                grid-template-columns: 1fr;
            }
        }

        /* =========================================================
   SUPPORT NETWORK VISUAL IDENTITY
   ========================================================= */

        .support-card-header {
            min-height: 48px;
        }

        .support-title-wrap {
            display: flex;
            align-items: center;
            gap: 11px;
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


        /* WhatsApp */

        .support-logo-whatsapp {
            background: #e8f8ef;
            color: #20a95b;
        }


        /* Instagram */

        .support-logo-instagram {
            background: #faeafa;
            color: #c135b7;
        }


        /* Telegram */

        .support-logo-telegram {
            background: #e8f5fd;
            color: #229ed9;
        }


        /* Rubika */

        .support-logo-rubika {
            background: #fff0e5;
            color: #e87528;
        }


        /* Eitaa */

        .support-logo-eitaa {
            background: #edf0ff;
            color: #405de6;
        }


        /* Bale */

        .support-logo-bale {
            background: #e8f7ff;
            color: #168dcc;
        }


        .support-title-wrap h3 {
            margin: 0 0 7px;
            font-size: 15px;
            font-weight: 900;
        }


    </style>

@endsection
