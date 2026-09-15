<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'پنل مدیریت') | SH</title>

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

@stack('scripts')

</body>
</html>
