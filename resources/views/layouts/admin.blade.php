<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'پنل مدیریت') | دوپینگ</title>

    @vite([
    'resources/css/app.css',
    'resources/css/admin.css',
    'resources/js/app.js',
    'resources/js/admin.js'
    ])
</head>

<body class="admin-body">

<div class="admin-app">

    <div class="admin-overlay" data-sidebar-overlay></div>

    @include('admin.components.sidebar')

    <div class="admin-main">

        @include('admin.components.navbar')

        <main class="admin-content">
            @yield('content')
        </main>

    </div>

</div>

<script>
(function () {
    const idleLimit = {{ (int) config('session.lifetime') * 60 * 1000 }};
    const loginUrl = @json(route('admin.login'));
    const storageKey = 'admin_last_activity';
    let lastEventTime = 0;
    let lastActivity;

    function readActivity() {
        try {
            return Number(localStorage.getItem(storageKey)) || 0;
        } catch (error) {
            return lastActivity || Date.now();
        }
    }

    function saveActivity(time) {
        lastActivity = time;
        try {
            localStorage.setItem(storageKey, String(time));
        } catch (error) {
            // Continue with the in-memory timestamp.
        }
    }

    function redirectToLogin() {
        window.location.replace(loginUrl);
    }

    lastActivity = readActivity();

    if (!lastActivity || Date.now() - lastActivity >= idleLimit) {
        saveActivity(Date.now());
    }

    function checkInactivity() {
        const last = readActivity();
        lastActivity = last;

        if (Date.now() - last >= idleLimit) {
            redirectToLogin();
        }
    }

    function registerActivity() {
        const now = Date.now();

        if (now - lastEventTime < 2000) {
            return;
        }

        lastEventTime = now;
        saveActivity(now);
    }

    [
        'mousemove',
        'mousedown',
        'keydown',
        'touchstart',
        'scroll',
        'click'
    ].forEach(function (eventName) {
        document.addEventListener(eventName, registerActivity, {
            passive: true
        });
    });

    window.addEventListener('storage', function (event) {
        if (event.key === storageKey) {
            lastActivity = Number(event.newValue) || lastActivity;
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            checkInactivity();
        }
    });

    window.addEventListener('focus', checkInactivity);

    setInterval(checkInactivity, 1000);
})();
</script>

@stack('scripts')

</body>
</html>
