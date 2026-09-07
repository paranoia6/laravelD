<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Minia Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f8f8fb] text-slate-700">

<div class="min-h-screen">

    <!-- Sidebar -->
    <aside class="fixed right-0 top-0 z-40 hidden h-screen w-[76px] border-l border-slate-200 bg-white lg:block">

        <!-- Logo -->
        <div class="flex h-[76px] items-center justify-center border-b border-slate-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-xl font-bold text-white">
                M
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex flex-col items-center gap-2 py-6">

            <a href="{{ route('demo.dashboard') }}"
               class="group flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <span class="text-xl">⌂</span>
            </a>

            <a href="{{ route('demo.users') }}"
               class="group flex h-12 w-12 items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600">
                <span class="text-xl">▦</span>
            </a>

            <a href="#"
               class="group flex h-12 w-12 items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600">
                <span class="text-xl">♙</span>
            </a>

            <a href="#"
               class="group flex h-12 w-12 items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600">
                <span class="text-xl">▣</span>
            </a>

            <a href="#"
               class="group flex h-12 w-12 items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600">
                <span class="text-xl">▤</span>
            </a>

            <a href="#"
               class="group flex h-12 w-12 items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600">
                <span class="text-xl">⚙</span>
            </a>

        </nav>

    </aside>


    <!-- Main -->
    <div class="lg:mr-[76px]">

        <!-- Topbar -->
        <header class="sticky top-0 z-30 h-[76px] border-b border-slate-200 bg-white">

            <div class="flex h-full items-center justify-between px-6">

                <!-- Right -->
                <div class="flex items-center gap-5">

                    <button class="text-xl text-slate-500">
                        ☰
                    </button>

                    <div class="relative hidden md:block">
                        <input
                            type="text"
                            placeholder="Search..."
                            class="h-10 w-64 rounded-md bg-slate-50 px-4 text-sm outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-100"
                        >

                        <button class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-l-md bg-indigo-600 text-white">
                            ⌕
                        </button>
                    </div>

                </div>


                <!-- Left -->
                <div class="flex items-center gap-5">

                    <button class="text-xl text-slate-500">
                        ◐
                    </button>

                    <button class="relative text-xl text-slate-500">
                        ♧
                        <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] text-white">
                            5
                        </span>
                    </button>

                    <span class="hidden text-sm text-slate-500 md:block">
                        🇺🇸
                    </span>

                    <div class="flex items-center gap-3 border-r pr-5">

                        <div class="text-left">
                            <p class="text-sm font-semibold text-slate-700">
                                مدیر سیستم
                            </p>
                            <p class="text-xs text-slate-400">
                                Administrator
                            </p>
                        </div>

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">
                            م
                        </div>

                    </div>

                </div>

            </div>

        </header>


        <!-- Content -->
        <main class="px-5 py-6 lg:px-8">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>
