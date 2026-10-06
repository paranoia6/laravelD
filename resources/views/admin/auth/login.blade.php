<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ورود به پنل مدیریت | Doping</title>

    @vite([
        'resources/css/app.css',
        'resources/css/admin.css',
        'resources/js/app.js',
        'resources/js/admin.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Vazirmatn, Tahoma, sans-serif;
            background: #09090b;
            color: #fff;
            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* =========================
           DOPING BRAND PANEL
        ========================= */

        .login-brand {
            width: 52%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border-left: 1px solid rgba(255,255,255,.06);

            background:
                radial-gradient(
                    circle at 50% 50%,
                    rgba(236,72,153,.07),
                    transparent 38%
                ),
                linear-gradient(
                    145deg,
                    #171018,
                    #09090b 65%
                );
        }

        .brand-orb {
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            border: 1px solid rgba(236,72,153,.10);

            box-shadow:
                0 0 0 70px rgba(236,72,153,.025),
                0 0 0 140px rgba(236,72,153,.018);

            pointer-events: none;
        }

        .brand-content {
            position: relative;
            z-index: 2;

            width: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;
        }

        .brand-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            gap: 14px;
        }

        .logo-mark {
            width: 68px;
            height: 68px;

            display: grid;
            place-items: center;

            border-radius: 20px;

            background:
                linear-gradient(
                    145deg,
                    #f472b6,
                    #db2777
                );

            box-shadow:
                0 18px 45px rgba(219,39,119,.28),
                inset 0 1px 0 rgba(255,255,255,.35);

            font-size: 32px;
            font-weight: 900;
            color: #fff;
        }

        .logo-text {
            font-size: 39px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -1.5px;
        }

        .logo-subtitle {
            margin-top: 8px;

            color: #71717a;

            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;

            direction: ltr;
            text-align: center;
        }

        /* =========================
           LOGIN PANEL
        ========================= */

        .login-panel {
            width: 48%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 40px;

            position: relative;
            z-index: 3;

            background:
                radial-gradient(
                    circle at 50% 20%,
                    rgba(236,72,153,.07),
                    transparent 38%
                ),
                #fff9fb;
        }

        .login-card {
            width: min(440px, 100%);

            padding: 42px;

            border: 1px solid #f0dce5;
            border-radius: 28px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.98),
                    rgba(255,248,251,.98)
                );

            box-shadow:
                0 35px 90px rgba(80,30,50,.16);

            color: #493943;
        }

        /* =========================
           LOGIN HEADER
        ========================= */

        .login-heading {
            margin-bottom: 32px;
        }

        .login-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .login-mini-logo {
            width: 42px;
            height: 42px;

            flex: 0 0 42px;

            display: grid;
            place-items: center;

            border-radius: 12px;

            background:
                linear-gradient(
                    145deg,
                    #f472b6,
                    #db2777
                );

            color: #fff;

            font-size: 20px;
            font-weight: 900;

            box-shadow:
                0 8px 20px rgba(219,39,119,.20);
        }

        .login-heading h1 {
            margin: 0;

            font-size: 27px;
            font-weight: 850;

            color: #493943;
            letter-spacing: -.5px;
        }

        .login-heading p {
            margin: 10px 0 0;

            color: #8c7b83;

            font-size: 13px;
            line-height: 1.8;
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;

            margin-bottom: 9px;

            color: #6e6268;

            font-size: 12px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            right: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #8d7d85;

            font-size: 16px;

            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 54px;

            padding: 0 46px 0 15px;

            border: 1px solid #ead5df;
            border-radius: 14px;

            outline: none;

            background: #fff;
            color: #493943;

            font-family: inherit;
            font-size: 13px;

            transition: .2s ease;
        }

        .form-input::placeholder {
            color: #aaa0a5;
        }

        .form-input:hover {
            border-color: #d9b8c5;
        }

        .form-input:focus {
            border-color: #ec4899;

            box-shadow:
                0 0 0 4px rgba(236,72,153,.09);

            background: #fff;
        }

        .form-input.is-invalid {
            border-color: #ef4444;
        }

        .password-toggle {
            position: absolute;

            left: 12px;
            top: 50%;

            transform: translateY(-50%);

            border: 0;
            background: transparent;

            color: #8d7d85;

            cursor: pointer;

            font-family: inherit;
            font-size: 11px;

            padding: 7px;
        }

        .password-toggle:hover {
            color: #db2777;
        }

        /* =========================
           OPTIONS
        ========================= */

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin: 3px 0 24px;
        }

        .remember {
            display: inline-flex;
            align-items: center;

            gap: 8px;

            color: #8c7b83;

            font-size: 11px;
            cursor: pointer;
        }

        .remember input {
            accent-color: #ec4899;
        }

        .secure-text {
            display: flex;
            align-items: center;

            gap: 6px;

            color: #8c7b83;

            font-size: 10px;
        }

        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-button {
            width: 100%;
            height: 55px;

            border: 0;
            border-radius: 14px;

            cursor: pointer;

            position: relative;
            overflow: hidden;

            color: #fff;

            font-family: inherit;
            font-size: 13px;
            font-weight: 800;

            background:
                linear-gradient(
                    135deg,
                    #ec4899,
                    #db2777
                );

            box-shadow:
                0 12px 30px rgba(219,39,119,.22),
                inset 0 1px 0 rgba(255,255,255,.25);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .login-button::before {
            content: "";

            position: absolute;
            inset: 0;

            background:
                linear-gradient(
                    110deg,
                    transparent 25%,
                    rgba(255,255,255,.20) 50%,
                    transparent 75%
                );

            transform: translateX(-100%);

            transition:
                transform .6s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 17px 38px rgba(219,39,119,.30),
                inset 0 1px 0 rgba(255,255,255,.25);
        }

        .login-button:hover::before {
            transform: translateX(100%);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* =========================
           ERRORS
        ========================= */

        .error-box {
            margin-bottom: 20px;

            padding: 13px 15px;

            border: 1px solid rgba(239,68,68,.25);
            border-radius: 13px;

            background: rgba(239,68,68,.08);

            color: #dc2626;

            font-size: 11px;
            line-height: 1.9;
        }

        .error-box ul {
            margin: 0;
            padding-right: 18px;
        }

        /* =========================
           FOOTER
        ========================= */

        .login-footer {
            margin-top: 27px;
            padding-top: 20px;

            border-top: 1px solid #f0e1e6;

            text-align: center;

            color: #9c8b92;

            font-size: 10px;
        }

        .login-footer strong {
            color: #c64d7c;
        }

        .version {
            margin-top: 8px;

            direction: ltr;

            font-size: 9px;

            color: #b5a8ad;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 950px) {

            .login-brand {
                width: 45%;
            }

            .login-panel {
                width: 55%;
                padding: 25px;
            }
        }

        @media (max-width: 760px) {

            .login-page {
                display: block;
            }

            .login-brand {
                display: none;
            }

            .login-panel {
                width: 100%;
                min-height: 100vh;

                padding: 24px;

                background:
                    radial-gradient(
                        circle at 50% 0%,
                        rgba(236,72,153,.10),
                        transparent 38%
                    ),
                    #fff9fb;
            }

            .login-card {
                padding: 30px 24px;
                border-radius: 24px;
            }

            .login-heading h1 {
                font-size: 24px;
            }

            .login-mini-logo {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }
        }

        /* =========================
           ANIMATION
        ========================= */

        @media (prefers-reduced-motion: no-preference) {

            .login-card {
                animation:
                    cardIn .65s cubic-bezier(.2,.8,.2,1) both;
            }

            .brand-content {
                animation:
                    brandIn .8s cubic-bezier(.2,.8,.2,1) both;
            }
        }

        @keyframes cardIn {

            from {
                opacity: 0;
                transform: translateY(18px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes brandIn {

            from {
                opacity: 0;
                transform: translateX(25px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>

<div class="login-page">

    <!-- DOPING -->
    <section class="login-brand">

        <div class="brand-orb"></div>

        <div class="brand-content">

            <div class="brand-logo">

                <div class="logo-mark">
                    D
                </div>

                <div>
                    <div class="logo-text">
                        Doping
                    </div>

                    <div class="logo-subtitle">
                        MANAGEMENT SYSTEM
                    </div>
                </div>

            </div>

        </div>

    </section>


    <!-- LOGIN -->
    <main class="login-panel">

        <div class="login-card">

            <div class="login-heading">

                <div class="login-title-row">

                    <div class="login-mini-logo">
                        D
                    </div>

                    <h1>
                        خوش آمدید 👋
                    </h1>

                </div>

                <p>
                    برای ورود به پنل مدیریت، اطلاعات حساب خود را وارد کنید.
                </p>

            </div>


            @if ($errors->any())

                <div class="error-box">

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('admin.login.submit') }}"
            >

                @csrf


                <div class="form-group">

                    <label
                        class="form-label"
                        for="email"
                    >
                        ایمیل
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            ✉
                        </span>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="form-input @error('email') is-invalid @enderror"
                            placeholder="admin@example.com"
                            autocomplete="email"
                            autofocus
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label
                        class="form-label"
                        for="password"
                    >
                        رمز عبور
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            ⌁
                        </span>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="رمز عبور خود را وارد کنید"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            aria-label="نمایش رمز عبور"
                        >
                            نمایش
                        </button>

                    </div>

                </div>


                <div class="form-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        مرا به خاطر بسپار

                    </label>


                    <span class="secure-text">

                        <span>🔒</span>

                        اتصال امن

                    </span>

                </div>


                <button
                    type="submit"
                    class="login-button"
                >
                    ورود به پنل مدیریت
                </button>

            </form>


            <div class="login-footer">

                دسترسی اختصاصی مدیریت
                <strong>Doping</strong>

                <div class="version">
                    DOPING ADMIN • v1.0
                </div>

            </div>

        </div>

    </main>

</div>


<script>
    function togglePassword() {

        const input =
            document.getElementById('password');

        const button =
            document.querySelector('.password-toggle');

        if (input.type === 'password') {

            input.type = 'text';

            button.textContent = 'مخفی';

        } else {

            input.type = 'password';

            button.textContent = 'نمایش';
        }
    }
</script>

</body>
</html>
