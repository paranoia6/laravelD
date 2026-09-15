<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'پنل مدیریت')</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>

<div class="admin-layout">

    @include('components.admin.sidebar')

    <main class="admin-main">

        @include('components.admin.header')

        <div class="admin-content">
            @yield('content')
        </div>

    </main>

</div>

<script src="{{ asset('js/admin.js') }}"></script>

</body>
</html>
